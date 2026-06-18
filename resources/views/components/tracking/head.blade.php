@php
    $renderer = app(\App\Services\Tracking\PixelRendererService::class);
    $debug = app(\App\Services\Tracking\DiagnosticsService::class)->isDebugMode();
@endphp

@if($debug)
    <script>window.__dtDebug = true;</script>
@endif

{!! $renderer->renderHead() !!}
