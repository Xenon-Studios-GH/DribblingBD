<?php

namespace App\Traits;

use App\Models\SeoMeta;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function getSeoTitleAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return SiteSetting::getValue('site_name', config('app.name'));
        }

        return $meta->meta_title ?: SiteSetting::getValue('site_name', config('app.name'));
    }

    public function getSeoDescriptionAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return SiteSetting::getValue('site_description', '');
        }

        return $meta->meta_description ?: SiteSetting::getValue('site_description', '');
    }

    public function getSeoKeywordsAttribute(): ?array
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return [];
        }

        return $meta->focus_keywords ?? [];
    }

    public function getSeoOgTitleAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return $this->getSeoTitleAttribute();
        }

        return $meta->og_title ?: $meta->meta_title ?: $this->getSeoTitleAttribute();
    }

    public function getSeoOgDescriptionAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return $this->getSeoDescriptionAttribute();
        }

        return $meta->og_description ?: $meta->meta_description ?: $this->getSeoDescriptionAttribute();
    }

    public function getSeoOgImageAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta || ! $meta->og_image) {
            return null;
        }

        return \Storage::disk('public')->url($meta->og_image);
    }

    public function getSeoCanonicalUrlAttribute(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return null;
        }

        return $meta->canonical_url;
    }

    public function getSeoRobotsAttribute(): string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return SiteSetting::getValue('seo_default_robots', 'index,follow');
        }

        return $meta->robots ?? SiteSetting::getValue('seo_default_robots', 'index,follow');
    }

    public function getSeoMetaTagHtml(): string
    {
        $meta = $this->seoMeta;
        if (! $meta) {
            return '';
        }

        $html = [];

        if ($meta->meta_title) {
            $html[] = '<meta name="title" content="'.e($meta->meta_title).'">';
        }
        if ($meta->meta_description) {
            $html[] = '<meta name="description" content="'.e($meta->meta_description).'">';
        }
        if (! empty($meta->focus_keywords)) {
            $html[] = '<meta name="keywords" content="'.e(implode(', ', $meta->focus_keywords)).'">';
        }
        if ($meta->og_title) {
            $html[] = '<meta property="og:title" content="'.e($meta->og_title).'">';
        }
        if ($meta->og_description) {
            $html[] = '<meta property="og:description" content="'.e($meta->og_description).'">';
        }
        if ($meta->og_image) {
            $html[] = '<meta property="og:image" content="'.e(\Storage::disk('public')->url($meta->og_image)).'">';
        }
        if ($meta->og_url) {
            $html[] = '<meta property="og:url" content="'.e($meta->og_url).'">';
        }
        if ($meta->twitter_title) {
            $html[] = '<meta name="twitter:title" content="'.e($meta->twitter_title).'">';
        }
        if ($meta->twitter_description) {
            $html[] = '<meta name="twitter:description" content="'.e($meta->twitter_description).'">';
        }
        if ($meta->twitter_image) {
            $html[] = '<meta name="twitter:image" content="'.e(\Storage::disk('public')->url($meta->twitter_image)).'">';
        }
        if ($meta->canonical_url) {
            $html[] = '<link rel="canonical" href="'.e($meta->canonical_url).'">';
        }
        if ($meta->robots && $meta->robots !== 'index,follow') {
            $html[] = '<meta name="robots" content="'.e($meta->robots).'">';
        }

        return implode("\n    ", $html);
    }

    public function getSeoJsonLd(): ?string
    {
        $meta = $this->seoMeta;
        if (! $meta || ! $meta->schema_markup) {
            return null;
        }

        return json_encode($meta->schema_markup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
