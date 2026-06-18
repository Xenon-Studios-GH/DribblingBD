@php
    $renderer = app(\App\Services\Tracking\PixelRendererService::class);
@endphp

{!! $renderer->renderBody() !!}

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.dt !== 'function') return;
    let firedDepths = new Set();
    const depths = [25, 50, 75, 100];
    window.addEventListener('scroll', function() {
        const scrollPct = Math.round((window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100);
        depths.forEach(d => {
            if (scrollPct >= d && !firedDepths.has(d)) {
                firedDepths.add(d);
                window.dt('ScrollDepth', { percentage: d });
            }
        });
    }, { passive: true });
});
</script>
