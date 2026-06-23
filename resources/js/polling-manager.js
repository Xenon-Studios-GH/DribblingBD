/**
 * PollingManager — centralized polling controller
 *
 * Replaces scattered setInterval() calls across Blade views with a single
 * managed system that:
 * - Pauses all polls when the browser tab is hidden (Page Visibility API)
 * - Resumes + immediately fires polls when the tab becomes visible again
 * - Prevents duplicate registration via name-based identity
 * - Provides cleanup hooks for Alpine.js destroy lifecycle
 * - Exposes status for the monitoring dashboard
 */
const STORAGE_KEY = 'pol_configs';

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

class PollingManager {
    constructor() {
        this._polls = new Map();
        this._isPaused = false;
        this._boundVisibilityHandler = this._onVisibilityChange.bind(this);
        this._syncQueue = new Set();
        this._syncTimer = null;
        this._serverConfigs = null;

        if (typeof document !== 'undefined') {
            document.addEventListener('visibilitychange', this._boundVisibilityHandler);
            this._fetchServerConfigs();
        }
    }

    async _fetchServerConfigs() {
        try {
            const res = await fetch('/controlPanel/tracker', {
                headers: { 'Accept': 'application/json' },
            });
            if (res.ok) {
                this._serverConfigs = await res.json();
            }
        } catch { /* server unavailable, use localStorage */ }
    }

    _getEffectiveConfig(name) {
        const local = this._loadSaved(name);
        const server = this._serverConfigs ? this._serverConfigs[name] : null;
        return {
            isActive: local?.isActive !== undefined ? local.isActive
                    : server?.is_active !== undefined ? server.is_active
                    : true,
            intervalMs: local?.intervalMs || server?.interval_ms || null,
        };
    }

    _loadSaved(name) {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            const configs = JSON.parse(raw);
            return configs[name] || null;
        } catch { return null; }
    }

    _save(name, data) {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            const configs = raw ? JSON.parse(raw) : {};
            configs[name] = { ...(configs[name] || {}), ...data };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(configs));
        } catch { /* localStorage unavailable */ }
    }

    _syncToServer(name, data) {
        this._syncQueue.add(name);
        if (!this._syncTimer) {
            this._syncTimer = setTimeout(() => this._flushSync(), 2000);
        }
        const existing = this._syncPayloads || {};
        existing[name] = { ...(existing[name] || {}), ...data };
        this._syncPayloads = existing;
    }

    _flushSync() {
        this._syncTimer = null;
        const payloads = this._syncPayloads || {};
        this._syncPayloads = {};
        const keys = Object.keys(payloads);
        if (keys.length === 0) return;

        for (const key of keys) {
            const data = payloads[key];
            fetch('/controlPanel/tracker/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                body: JSON.stringify({
                    key: key,
                    type: 'poll',
                    is_active: data.isActive !== undefined ? data.isActive : true,
                    interval_ms: data.intervalMs || PollingManager.DEFAULT_INTERVALS[key] || 60000,
                    last_run_at: data.lastRunAt || null,
                    run_count: data.runCount || null,
                }),
            }).catch(() => {});
        }
    }

    static DEFAULT_INTERVALS = {
        'dashboard-clock': 60000,
        'dashboard-kpi': 60000,
        'orders-reload': 60000,
        'orders-stock-check': 30000,
        'notification-bell': 30000,
        'stock-table': 60000,
        'order-create-stock': 30000,
        'order-edit-stock': 30000,
    };

    /**
     * Register a poll.
     *
     * @param {string}   name        Unique name (e.g. 'orders-reload')
     * @param {Function} callback    Function to call each tick
     * @param {object}   options
     * @param {boolean}  options.immediate  Fire once immediately (default false)
     * @param {string}   options.page       Page this poll belongs to (for debugging)
     */
    add(name, callback, options = {}) {
        if (this._polls.has(name)) {
            this.remove(name);
        }

        const cfg = this._getEffectiveConfig(name);
        const isActive = cfg.isActive;
        const safeInterval = Math.max(5000, cfg.intervalMs || PollingManager.DEFAULT_INTERVALS[name] || 60000);

        const poll = {
            name,
            callback,
            intervalMs: safeInterval,
            timerId: null,
            isActive,
            page: options.page || 'unknown',
            lastRun: null,
            runCount: 0,
        };

        this._polls.set(name, poll);

        if (isActive && !this._isPaused) {
            poll.timerId = setInterval(() => this._tick(name), safeInterval);
        }

        if (options.immediate) {
            this._execute(poll);
        }
    }

    /**
     * Remove a poll by name.
     */
    remove(name) {
        const poll = this._polls.get(name);
        if (!poll) return;

        if (poll.timerId) {
            clearInterval(poll.timerId);
            poll.timerId = null;
        }
        this._polls.delete(name);
    }

    /**
     * Pause all active polls (called on tab hidden).
     */
    pause() {
        this._isPaused = true;
        for (const poll of this._polls.values()) {
            if (poll.timerId) {
                clearInterval(poll.timerId);
                poll.timerId = null;
            }
        }
    }

    /**
     * Resume all active polls (called on tab visible).
     * Optionally triggers an immediate refresh of all polls.
     */
    resume(triggerImmediate = false) {
        this._isPaused = false;
        for (const poll of this._polls.values()) {
            if (poll.isActive && !poll.timerId) {
                poll.timerId = setInterval(() => this._tick(poll.name), poll.intervalMs);
                if (triggerImmediate) {
                    this._execute(poll);
                }
            }
        }
    }

    /**
     * Pause a single poll.
     */
    pausePoll(name, defaultIntervalMs) {
        const poll = this._polls.get(name);
        const intervalMs = poll?.intervalMs || defaultIntervalMs || PollingManager.DEFAULT_INTERVALS[name] || 60000;
        if (poll) {
            poll.isActive = false;
            if (poll.timerId) {
                clearInterval(poll.timerId);
                poll.timerId = null;
            }
        }
        this._save(name, { isActive: false, intervalMs });
        this._syncToServer(name, { isActive: false, intervalMs });
    }

    /**
     * Resume a single poll.
     */
    resumePoll(name, defaultIntervalMs) {
        const poll = this._polls.get(name);
        if (!poll) {
            const ms = defaultIntervalMs || PollingManager.DEFAULT_INTERVALS[name] || 60000;
            this._save(name, { isActive: true, intervalMs: ms });
            this._syncToServer(name, { isActive: true, intervalMs: ms });
            return;
        }
        poll.isActive = true;
        if (!poll.timerId && !this._isPaused) {
            poll.timerId = setInterval(() => this._tick(poll.name), poll.intervalMs);
        }
        this._save(name, { isActive: true, intervalMs: poll.intervalMs });
        this._syncToServer(name, { isActive: true, intervalMs: poll.intervalMs });
    }

    /**
     * Immediately fire all active polls (e.g. when returning to a tab).
     */
    pollAll() {
        for (const poll of this._polls.values()) {
            if (poll.isActive) {
                this._execute(poll);
            }
        }
    }

    /**
     * Get status of all registered polls (for monitoring dashboard).
     */
    getStatus() {
        const status = {};
        for (const [name, poll] of this._polls) {
            status[name] = {
                name: poll.name,
                intervalMs: poll.intervalMs,
                intervalSec: Math.round(poll.intervalMs / 1000),
                isActive: poll.isActive,
                page: poll.page,
                lastRun: poll.lastRun ? new Date(poll.lastRun).toLocaleTimeString() : null,
                runCount: poll.runCount,
                description: this._getDescription(poll.name),
            };
        }
        return status;
    }

    /**
     * Change the interval of a running poll.
     */
    setInterval(name, intervalMs) {
        const safeInterval = Math.max(5000, intervalMs);
        const poll = this._polls.get(name);

        if (poll) {
            poll.intervalMs = safeInterval;
            if (poll.timerId) {
                clearInterval(poll.timerId);
                poll.timerId = null;
            }
            if (poll.isActive && !this._isPaused) {
                poll.timerId = setInterval(() => this._tick(name), safeInterval);
            }
        }

        const isActive = poll ? poll.isActive : true;
        this._save(name, { intervalMs: safeInterval, isActive });
        this._syncToServer(name, { intervalMs: safeInterval, isActive });
        return true;
    }

    /**
     * Immediately fire a single poll.
     */
    pollNow(name) {
        const poll = this._polls.get(name);
        if (poll && poll.isActive) {
            this._execute(poll);
        }
    }

    /**
     * Reset all poll timers — clears and restarts every active poll.
     * Called on page load to sync timers.
     */
    resetAll() {
        for (const poll of this._polls.values()) {
            if (poll.timerId) {
                clearInterval(poll.timerId);
                poll.timerId = null;
            }
            if (poll.isActive && !this._isPaused) {
                poll.timerId = setInterval(() => this._tick(poll.name), poll.intervalMs);
                this._execute(poll);
            }
        }
    }

    resetAllAndClearSaved() {
        this.resetSavedConfigs();
        this.resetAll();
    }

    _getDescription(name) {
        const descriptions = {
            'dashboard-clock': 'Updates the dashboard datetime display',
            'dashboard-kpi': 'Refreshes KPI cards with latest statistics',
            'orders-reload': 'Fetches latest orders and drafts list',
            'orders-stock-check': 'Checks for out-of-stock orders and auto-restores',
            'notification-bell': 'Polls unread notification count',
            'stock-table': 'Refreshes the stock management table',
            'order-create-stock': 'Checks stock availability per product during order creation',
            'order-edit-stock': 'Checks stock availability per product during order editing',
        };
        return descriptions[name] || 'Polling task';
    }

    /**
     * Get count of active polls.
     */
    get activeCount() {
        return [...this._polls.values()].filter(p => p.isActive && p.timerId).length;
    }

    /**
     * Check if paused.
     */
    get isPaused() {
        return this._isPaused;
    }

    /**
     * Get all saved configs from localStorage.
     */
    getSavedConfigs() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch { return {}; }
    }

    /**
     * Clear all saved configs from localStorage.
     */
    resetSavedConfigs() {
        localStorage.removeItem(STORAGE_KEY);
    }

    _tick(name) {
        const poll = this._polls.get(name);
        if (!poll || !poll.isActive || this._isPaused) return;
        this._execute(poll);
    }

    _execute(poll) {
        try {
            poll.callback();
            poll.lastRun = Date.now();
            poll.runCount++;
            this._save(poll.name, { isActive: poll.isActive, intervalMs: poll.intervalMs, lastRunAt: Date.now() });
            this._syncToServer(poll.name, { isActive: poll.isActive, intervalMs: poll.intervalMs, lastRunAt: Date.now() });
        } catch (e) {
            console.error(`[PollingManager] Error in "${poll.name}":`, e);
        }
    }

    _onVisibilityChange() {
        if (document.hidden) {
            this.pause();
        } else {
            this.resume(true);
        }
    }
}

const pollingManager = new PollingManager();
window.PollingManager = pollingManager;
export default pollingManager;
