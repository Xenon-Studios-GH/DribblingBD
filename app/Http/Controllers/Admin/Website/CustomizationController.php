<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\PendingImageDeletion;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomizationController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'faqs');
        $faqs = Faq::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();

        return view('website.customization', compact('tab', 'faqs', 'testimonials'));
    }

    // === AJAX get single items ===

    public function getFaq(string $role, Faq $faq)
    {
        return response()->json($faq);
    }

    public function getTestimonial(string $role, Testimonial $testimonial)
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
            'is_active' => 'boolean',
        ]);

        $faq = Faq::create($data + ['sort_order' => $data['sort_order'] ?? 0, 'is_active' => $data['is_active'] ?? true]);

        $this->workLogService->log('FAQ Created', 'website', $faq->id, "FAQ '{$faq->question}' created");

        return redirect(admin_route('website.customization.index', ['tab' => 'faqs']))
            ->with('success', 'FAQ created.');
    }

    public function updateFaq(string $role, Request $request, Faq $faq)
    {
        $data = $request->validate([
            'category' => 'required|in:product,order',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq->update($data);

        $this->workLogService->log('FAQ Updated', 'website', $faq->id, "FAQ updated");

        return redirect(admin_route('website.customization.index', ['tab' => 'faqs']))
            ->with('success', 'FAQ updated.');
    }

    public function destroyFaq(string $role, Faq $faq)
    {
        $faq->delete();
        $this->workLogService->log('FAQ Deleted', 'website', $faq->id, "FAQ deleted");
        return redirect(admin_route('website.customization.index', ['tab' => 'faqs']))
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
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $ext = $request->file('image')->extension();
            $filename = 'testimonial-' . Str::slug($data['name']) . '-' . time() . '.' . $ext;
            $data['image'] = $request->file('image')->storeAs('testimonials', $filename, 'public');
        }

        $testimonial = Testimonial::create($data + ['sort_order' => $data['sort_order'] ?? 0, 'is_active' => $data['is_active'] ?? true]);

        $this->workLogService->log('Testimonial Created', 'website', $testimonial->id, "Testimonial by '{$testimonial->name}' created");

        return redirect(admin_route('website.customization.index', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial created.');
    }

    public function updateTestimonial(string $role, Request $request, Testimonial $testimonial)
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
                PendingImageDeletion::create([
                    'file_path' => $testimonial->image,
                    'disk' => 'public',
                    'scheduled_for_deletion_at' => now()->addDays(30),
                ]);
            }
            $ext = $request->file('image')->extension();
            $filename = 'testimonial-' . Str::slug($data['name']) . '-' . time() . '.' . $ext;
            $data['image'] = $request->file('image')->storeAs('testimonials', $filename, 'public');
        }

        $testimonial->update($data);

        $this->workLogService->log('Testimonial Updated', 'website', $testimonial->id, "Testimonial by '{$testimonial->name}' updated");

        return redirect(admin_route('website.customization.index', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial updated.');
    }

    public function destroyTestimonial(string $role, Testimonial $testimonial)
    {
        if ($testimonial->image) {
            PendingImageDeletion::create([
                'file_path' => $testimonial->image,
                'disk' => 'public',
                'scheduled_for_deletion_at' => now()->addDays(30),
            ]);
        }
        $testimonial->delete();
        $this->workLogService->log('Testimonial Deleted', 'website', $testimonial->id, "Testimonial by '{$testimonial->name}' deleted");
        return redirect(admin_route('website.customization.index', ['tab' => 'testimonials']))
            ->with('success', 'Testimonial deleted.');
    }

    // === Settings ===

    public function updateSettings(string $role, Request $request)
    {
        if (!in_array($role, ['superadmin'])) {
            abort(403);
        }

        $allowed = [
            'hero_heading_top', 'hero_heading_middle', 'hero_heading_bottom',
            'hero_subtitle', 'hero_cta_text', 'hero_cta_link',
            'stats_1_value', 'stats_1_label', 'stats_1_suffix',
            'stats_2_value', 'stats_2_label', 'stats_2_suffix',
            'stats_3_value', 'stats_3_label', 'stats_3_suffix',
            'stats_4_value', 'stats_4_label', 'stats_4_suffix',
            'contact_phone', 'contact_email', 'contact_address',
            'contact_phone_label', 'contact_email_label', 'contact_address_label',
            'contact_whatsapp_label', 'contact_whatsapp',
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

            // Customer Care - Delivery
            'delivery_heading', 'delivery_subtitle', 'delivery_desc',
            'delivery_dhaka', 'delivery_division', 'delivery_other',

            // Customer Care - Shipping info
            'shipping_charge_heading', 'shipping_charge_text',
            'shipping_cod_heading', 'shipping_cod_text',
            'shipping_tracking_heading', 'shipping_tracking_text',

            // Customer Care - Page
            'customer_care_heading', 'customer_care_subtitle',
            'customer_care_shipping_heading', 'customer_care_shipping_subtitle',
            'customer_care_contact_heading', 'customer_care_contact_subtitle',
            'inquiry_heading', 'inquiry_subtitle', 'inquiry_response_time',
            'inquiry_submit_button', 'inquiry_choose_image', 'inquiry_no_file',

            // Footer
            'footer_newsletter_heading', 'footer_newsletter_desc',
            'footer_subscribe_button', 'footer_brand_description',
            'footer_copyright_text', 'footer_we_accept_text',

            // FAQ Page
            'faq_page_heading', 'faq_page_subtitle',
            'faq_product_heading', 'faq_product_empty',
            'faq_order_heading', 'faq_order_empty',

            // General UI Labels
            'ui_view_all', 'ui_quick_view', 'ui_add_to_cart', 'ui_added',
            'ui_view_all_new_arrivals', 'ui_view_all_top_selling',
            'ui_no_results_for', 'ui_search_placeholder',
            'ui_shopping_cart', 'ui_cart_empty', 'ui_cart_empty_desc',
            'ui_subtotal', 'ui_shipping', 'ui_free', 'ui_total',
            'ui_proceed_checkout', 'ui_continue_shopping',
            'ui_checkout', 'ui_checkout_desc', 'ui_checkout_empty', 'ui_checkout_empty_desc',
            'ui_products_label', 'ui_shipping_address', 'ui_summary',
            'ui_place_order', 'ui_back_to_cart',
            'ui_view_cart',

            // Wishlist
            'ui_my_wishlist', 'ui_wishlist_empty', 'ui_wishlist_empty_desc',
            'ui_browse_jerseys', 'ui_remove',

            // Profile
            'ui_personal_info', 'ui_address_label', 'ui_preferences',
            'ui_wishlist_label', 'ui_wishlist_empty_profile', 'ui_wishlist_empty_profile_desc',
            'ui_browse_products', 'ui_save_changes', 'ui_view',
            'ui_order_history', 'ui_order_number', 'ui_no_orders_yet', 'ui_no_orders_desc',
            'ui_start_shopping', 'ui_cart_label', 'ui_cart_empty_profile', 'ui_cart_empty_profile_desc',

            // Processing Order
            'ui_processing_order', 'ui_processing_order_desc',
            'ui_order_handed_over', 'ui_order_handed_over_desc',
            'ui_confirm_whatsapp', 'ui_back_to_home',

            // Product detail
            'ui_you_may_also_like', 'ui_in_stock', 'ui_out_of_stock',
            'ui_no_stock', 'ui_contact_stock', 'ui_free_shipping_text',
            'ui_delivery_time_text', 'ui_saved', 'ui_save',

            // Product listing
            'ui_all_label', 'ui_in_stock_filter', 'ui_out_of_stock_filter',
            'ui_no_products_yet', 'ui_no_products_desc',
            'ui_no_projects', 'ui_all_jerseys', 'ui_player_edition',

            // Notifications
            'ui_notify_added_cart', 'ui_notify_saved_wishlist', 'ui_notify_removed_wishlist',

            // SEO
            'site_name',
            'site_description',
            'seo_default_robots',
            'seo_default_og_image',
            'seo_canonical_base',
        ];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                SiteSetting::setValue($key, $request->input($key));
            }
        }

        Cache::forget('site_settings');

        // Handle hero image uploads
        for ($i = 1; $i <= 3; $i++) {
            if ($request->hasFile("hero_image_{$i}")) {
                $existing = SiteSetting::getValue("hero_image_{$i}");
                if ($existing) {
                    PendingImageDeletion::create([
                        'file_path' => $existing,
                        'disk' => 'public',
                        'scheduled_for_deletion_at' => now()->addDays(30),
                    ]);
                }
                $ext = $request->file("hero_image_{$i}")->extension();
                $filename = 'hero-' . $i . '-' . time() . '.' . $ext;
                $path = $request->file("hero_image_{$i}")->storeAs('hero', $filename, 'public');
                SiteSetting::setValue("hero_image_{$i}", $path);
            }
            if ($request->has("remove_hero_image_{$i}")) {
                $existing = SiteSetting::getValue("hero_image_{$i}");
                if ($existing) {
                    PendingImageDeletion::create([
                        'file_path' => $existing,
                        'disk' => 'public',
                        'scheduled_for_deletion_at' => now()->addDays(30),
                    ]);
                }
                SiteSetting::setValue("hero_image_{$i}", null);
            }
        }

        $this->workLogService->log('Settings Updated', 'website', null, 'Site settings updated');

        return redirect(admin_route('website.customization.index', ['tab' => 'settings']))
            ->with('success', 'Settings saved.');
    }
}
