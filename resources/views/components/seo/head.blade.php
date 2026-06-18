@props(['model' => null, 'page' => null, 'overrides' => []])

@php
    use App\Models\SeoSetting;

    $seo = null;
    $staticPage = null;

    if ($model && method_exists($model, 'seoMeta') && $model->relationLoaded('seoMeta') && $model->seoMeta) {
        $seo = $model->seoMeta;
    } elseif ($model && method_exists($model, 'seoMeta') && !$model->relationLoaded('seoMeta')) {
        $seo = $model->seoMeta()->first();
    } elseif ($page) {
        $staticPage = config('seo.static_pages.' . $page, []);
    }

    $siteName = SeoSetting::getValue('site_name') ?? config('app.name');

    if (!$seo && $staticPage) {
        $replacements = [
            '{site_name}' => $siteName,
            '{current_year}' => now()->format('Y'),
            '{brand}' => $siteName,
        ];
        $seo = array_map(fn($v) => is_string($v) ? str_replace(array_keys($replacements), array_values($replacements), $v) : $v, $staticPage);
    }

    $seoGet = function ($key, $default = null) use ($seo) {
        if (is_object($seo) && property_exists($seo, $key) && !is_null($seo->$key)) return $seo->$key;
        if (is_array($seo) && array_key_exists($key, $seo) && !is_null($seo[$key])) return $seo[$key];
        return $default;
    };
    $titleSuffix = SeoSetting::getValue('title_suffix') ?? ' | ' . $siteName;
    $defaultOgImage = SeoSetting::getValue('default_og_image') ?? config('seo.default_og_image');
    $twitterHandle = SeoSetting::getValue('twitter_handle');
    $currentUrl = url()->current();

    $title = $overrides['meta_title'] ?? $seoGet('meta_title', $siteName);
    if (!str_contains($title, $siteName) && !$page) {
        $title .= $titleSuffix;
    }

    $metaDescription = $overrides['meta_description'] ?? $seoGet('meta_description', '');
    $focusKeywords = $overrides['focus_keywords'] ?? $seoGet('focus_keywords', '');
    $keywords = is_array($focusKeywords) ? implode(', ', $focusKeywords) : $focusKeywords;
    $robots = $overrides['robots'] ?? $seoGet('robots', config('seo.default_robots', 'index,follow'));
    $canonical = $overrides['canonical_url'] ?? $seoGet('canonical_url', $currentUrl);

    $ogTitle = $overrides['og_title'] ?? $seoGet('og_title', $title);
    $ogDescription = $overrides['og_description'] ?? $seoGet('og_description', $metaDescription);
    $ogImage = $overrides['og_image'] ?? $seoGet('og_image', $defaultOgImage);
    $ogUrl = $overrides['og_url'] ?? $seoGet('og_url', $currentUrl);
    $ogType = $overrides['og_type'] ?? $seoGet('og_type', 'website');

    $twitterTitle = $overrides['twitter_title'] ?? $seoGet('twitter_title', $ogTitle);
    $twitterDescription = $overrides['twitter_description'] ?? $seoGet('twitter_description', $ogDescription);
    $twitterImage = $overrides['twitter_image'] ?? $seoGet('twitter_image', $ogImage);

    $hreflang = $seoGet('hreflang', []);
    $schemaType = $seoGet('schema_type');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:type" content="{{ $ogType }}">
@if($ogImage)
<meta property="og:image" content="{{ storage_url($ogImage) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta property="og:site_name" content="{{ $siteName }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
@if($twitterImage)
<meta name="twitter:image" content="{{ storage_url($twitterImage) }}">
@endif
@if($twitterHandle)
<meta name="twitter:site" content="{{ $twitterHandle }}">
@endif

@if(is_array($hreflang))
    @foreach($hreflang as $lang => $href)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $href }}">
    @endforeach
@endif

@if($seo && $schemaType)
    @php
        $seoForLd = is_array($seo) ? (object) $seo : $seo;
        $dummy = new class($seoForLd) {
            public $seoMeta;
            public function __construct($seoForLd) { $this->seoMeta = $seoForLd; }
        };
        $jsonLd = app(\App\Services\SeoService::class)->buildJsonLd($dummy);
    @endphp
    @if($jsonLd)
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    @endif
@endif
