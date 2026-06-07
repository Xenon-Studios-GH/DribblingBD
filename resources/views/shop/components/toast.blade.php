<div id="toast" style="display:none; position:fixed; top:24px; right:24px; z-index:99999; font-family:system-ui,-apple-system,sans-serif;">
    <div id="toast-inner" style="display:flex; align-items:stretch; min-width:340px; max-width:420px; border-radius:16px; background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); box-shadow:0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05); overflow:hidden; transform:translateX(120%) scale(0.9); transition:all 0.5s cubic-bezier(0.34,1.56,0.64,1);">
        <div id="toast-accent" style="width:5px; flex-shrink:0; background:#16a34a;"></div>
        <div style="display:flex; align-items:center; gap:14px; padding:16px 18px; flex:1;">
            <div id="toast-icon-wrap" style="width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:#16a34a; color:white; font-size:18px; transition:all 0.3s;">
                <i id="toast-icon" class="fas fa-check-circle" style="filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div id="toast-title" style="font-size:14px; font-weight:600; color:#0f172a; line-height:1.3; margin-bottom:2px;">Success</div>
                <div id="toast-msg" style="font-size:13px; color:#64748b; line-height:1.4; word-wrap:break-word;">Added to cart!</div>
            </div>
            <button onclick="document.getElementById('toast').style.display='none'" style="width:28px; height:28px; border-radius:8px; border:none; background:transparent; color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all 0.2s; font-size:14px; padding:0;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#475569'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">✕</button>
        </div>
        <div id="toast-progress-track" style="position:absolute; bottom:0; left:5px; right:0; height:3px; background:rgba(0,0,0,0.06);">
            <div id="toast-progress" style="height:100%; width:100%; background:#16a34a; border-radius:0 0 16px 0; transition:width 0.1s linear;"></div>
        </div>
    </div>
</div>
