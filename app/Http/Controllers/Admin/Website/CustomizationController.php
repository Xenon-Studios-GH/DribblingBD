<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomizationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'faqs');
        $faqs = Faq::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();

        return view('website.customization', compact('tab', 'faqs', 'testimonials'));
    }

    // === AJAX get single items ===

    public function getFaq(Faq $faq)
    {
        return response()->json($faq);
    }

    public function getTestimonial(Testimonial $testimonial)
    {
        return response()->json($testimonial);
    }

    // === FAQs ===

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|in:product,order',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Faq::create($data + ['sort_order' => $data['sort_order'] ?? 0]);

        return redirect(admin_route('website.customization', ['tab' => 'faqs']))
            ->with('success', 'FAQ created.');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'category' => 'required|in:product,order',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq->update($data);

        return redirect(admin_route('website.customization', ['tab' => 'faqs']))
            ->with('success', 'FAQ updated.');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        return redirect(admin_route('website.customization', ['tab' => 'faqs']))
            ->with('success', 'FAQ deleted.');
    }

    // === Testimonials ===

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create($data + ['sort_order' => $data['sort_order'] ?? 0]);

        return redirect(admin_route('website.customization', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial created.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return redirect(admin_route('website.customization', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial updated.');
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();
        return redirect(admin_route('website.customization', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial deleted.');
    }

    // === Settings ===

    public function updateSettings(Request $request)
    {
        $allowed = [
            'hero_heading_top', 'hero_heading_middle', 'hero_heading_bottom',
            'hero_subtitle', 'hero_cta_text', 'hero_cta_link',
            'stats_1_value', 'stats_1_label', 'stats_1_suffix',
            'stats_2_value', 'stats_2_label', 'stats_2_suffix',
            'stats_3_value', 'stats_3_label', 'stats_3_suffix',
            'stats_4_value', 'stats_4_label', 'stats_4_suffix',
            'contact_phone', 'contact_email', 'contact_address',
            'social_facebook', 'social_instagram', 'social_whatsapp',
            'feature_1_title', 'feature_1_desc',
            'feature_2_title', 'feature_2_desc',
            'feature_3_title', 'feature_3_desc',
            'feature_4_title', 'feature_4_desc',
            'new_arrivals_eyebrow', 'new_arrivals_heading',
            'top_selling_eyebrow', 'top_selling_heading',
            'testimonials_eyebrow', 'testimonials_heading',
            'banner_heading', 'banner_subtext', 'banner_cta', 'banner_cta_link',

            // Shipping settings
            'shipping_dhaka_rate',
            'shipping_outside_rate',
            'shipping_free_threshold',
        ];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                SiteSetting::setValue($key, $request->input($key));
            }
        }

        return redirect(admin_route('website.customization', ['tab' => 'settings']))
            ->with('success', 'Settings saved.');
    }
}
