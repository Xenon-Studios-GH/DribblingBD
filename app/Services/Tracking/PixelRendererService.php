<?php

namespace App\Services\Tracking;

use App\Models\TrackingPixel;
use App\Models\TrackingSetting;
use Illuminate\Support\Facades\Cache;

class PixelRendererService
{
    public function renderHead(): string
    {
        $pixels = $this->getActivePixels('head');

        $html = $this->initDribblingTrack();

        if ($pixels->isEmpty()) return $html;

        foreach ($pixels as $pixel) {
            $html .= match ($pixel->platform) {
                'meta' => $this->renderMetaHead($pixel),
                'ga4' => $this->renderGa4Head($pixel),
                'gtm' => $this->renderGtmHead($pixel),
                'google_ads' => $this->renderGoogleAdsHead($pixel),
                'clarity' => $this->renderClarityHead($pixel),
                default => '',
            };
        }

        return $html;
    }

    public function renderBody(): string
    {
        $pixels = $this->getActivePixels('body');
        if ($pixels->isEmpty()) return '';

        $html = "<!-- Tracking Body -->\n";

        foreach ($pixels as $pixel) {
            $html .= match ($pixel->platform) {
                'meta' => $this->renderMetaBody($pixel),
                'gtm' => $this->renderGtmBody($pixel),
                default => '',
            };
        }

        return $html;
    }

    public function getActivePixels(string $position = null): \Illuminate\Support\Collection
    {
        $key = 'tracking.active_pixels';
        $pixels = Cache::remember($key, 3600, function () {
            return TrackingPixel::active()->ordered()->get();
        });

        if ($position) {
            return $pixels->where('load_position', $position);
        }

        return $pixels;
    }

    public function clearCache(): void
    {
        Cache::forget('tracking.active_pixels');
        Cache::forget('tracking.settings');
    }

    protected function initDribblingTrack(): string
    {
        return <<<'JS'
<script>
window.dt=function(n,d){d=d||{};d.event_id=d.event_id||(Date.now().toString(36)+Math.random().toString(36).slice(2,8));try{if(window.fbq)fbq('track',n,d);}catch(e){}try{if(window.gtag)gtag('event',n,d);}catch(e){}if(window.__dtDebug)console.log('[DT]',n,d);if(navigator.sendBeacon){var b=JSON.stringify({event:n,data:d});navigator.sendBeacon('/__tracking/capi',b);}};
</script>
JS;
    }

    protected function renderMetaHead(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$id}');
fbq('track', 'PageView');
</script>
HTML;
    }

    protected function renderMetaBody(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={$id}&ev=PageView&noscript=1" alt=""/></noscript>
HTML;
    }

    protected function renderGa4Head(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$id}');</script>
HTML;
    }

    protected function renderGtmHead(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$id}');</script>
HTML;
    }

    protected function renderGtmBody(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$id}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
    }

    protected function renderGoogleAdsHead(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<script>gtag('config','{$id}');</script>
HTML;
    }

    protected function renderClarityHead(TrackingPixel $pixel): string
    {
        $id = e($pixel->pixel_id);
        return <<<HTML
<script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,'clarity','script','{$id}');</script>
HTML;
    }
}
