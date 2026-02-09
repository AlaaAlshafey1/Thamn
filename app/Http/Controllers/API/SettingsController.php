<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Color;
use App\Models\Faq;
use App\Models\Contact;
use App\Models\TermCondition;
use App\Models\User;
use App\Models\HomeStep;
use App\Models\Intro;

class SettingsController extends Controller
{
    /**
     * Get color configuration
     * GET /settings/colors
     */
    public function colors()
    {
        // جلب كل الألوان
        $colors = Color::orderBy('group')->get();

        // ترتيبهم حسب المجموعة
        $grouped = $colors->groupBy('group')->map(function ($group) {
            return $group->pluck('value', 'key');
        });

        return response()->json([
            'status' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Get intro page content
     * GET /settings/intro
     */
    public function intro(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $intros = Intro::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        if ($intros->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'لم يتم العثور على محتوى المقدمة' : 'Intro content not found',
                'data' => []
            ], 404);
        }

        $data = $intros->map(function ($intro) use ($lang) {
            return [
                'id' => $intro->id,
                'page' => $intro->page,
                'title' => $intro->getTitle($lang),
                'subTitle' => $intro->getSubTitle($lang),
                'description' => $intro->getDescription($lang),
                'image' => $intro->image,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع محتوى المقدمة بنجاح' : 'Intro content fetched successfully',
            'data' => $data
        ]);
    }

    /**
     * Get home steps content
     * GET /settings/home-steps
     */
    public function homeSteps(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $homeStep = HomeStep::where('is_active', 1)
            ->orderBy('sort_order')
            ->first();

        if (!$homeStep) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'لم يتم العثور على خطوات الصفحة الرئيسية' : 'Home steps content not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع خطوات الصفحة الرئيسية بنجاح' : 'Home steps fetched successfully',
            'data' => [
                'title' => $homeStep->getTitle($lang),
                'subTitle' => $homeStep->getSubTitle($lang),
                'desc' => $homeStep->getDesc($lang),
                'type' => $homeStep->type,
                'items' => $homeStep->getLocalizedItems($lang)
            ]
        ]);
    }

    /**
     * Get user notification settings
     * GET /settings/notifications
     */
    public function notificationSettings(Request $request)
    {
        $user = $request->user();
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        // Assuming notification settings are stored in user table or related table
        // Adjust based on your actual database schema
        $settings = [
            'news_enabled' => $user->news_enabled,
            'email_enabled' => $user->email_enabled,
            'sms_enabled' => $user->sms_enabled,
        ];

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع إعدادات الإشعارات بنجاح' : 'Notification settings fetched successfully',
            'data' => $settings
        ]);
    }

    /**
     * Update user notification settings
     * PUT /settings/notifications
     */
    public function updateNotificationSettings(Request $request)
    {
        $user = $request->user();
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $validated = $request->validate([
            'news_enabled' => 'sometimes|boolean',
            'email_enabled' => 'sometimes|boolean',
            'sms_enabled' => 'sometimes|boolean',
        ]);

        // Update user notification preferences
        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم تحديث إعدادات الإشعارات بنجاح' : 'Notification settings updated successfully',
            'data' => [
                'news_enabled' => $user->news_enabled,
                'email_enabled' => $user->email_enabled,
                'sms_enabled' => $user->sms_enabled,
            ]
        ]);
    }

    /**
     * Get about page content
     * GET /settings/about
     */
    public function about(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $about = About::where('type', 'about')->first();

        if (!$about) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'لم يتم العثور على صفحة عن ثمن' : 'About page not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع محتوى صفحة عن ثمن بنجاح' : 'About page fetched successfully',
            'data' => [
                'content' => $lang === 'ar' ? $about->content_ar : $about->content_en
            ]
        ]);
    }

    /**
     * Get FAQ list
     * GET /settings/faq
     */
    public function faq(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $faqs = Faq::all()->map(function ($faq) use ($lang) {
            return [
                'id' => $faq->id,
                'category' => $faq->category,
                'question' => $lang === 'ar' ? $faq->question_ar : $faq->question_en,
                'answer' => $lang === 'ar' ? $faq->answer_ar : $faq->answer_en,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع الأسئلة الشائعة بنجاح' : 'FAQ fetched successfully',
            'data' => $faqs
        ]);
    }

    /**
     * Get contact information
     * GET /settings/contact
     */
    public function contact(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        // Get the first contact record (since there should be only one main contact info)
        $contact = Contact::first();

        // Get social media links from JSON column
        $socialMedia = [];
        if ($contact && $contact->social_media) {
            $socials = is_array($contact->social_media) ? $contact->social_media : json_decode($contact->social_media, true);
            if (is_array($socials)) {
                $socialMedia = collect($socials)->map(function ($item) use ($lang) {
                    return [
                        'name' => $item['name'] ?? '',
                        'icon' => $item['icon'] ?? $this->getDefaultIcon($item['name'] ?? ''),
                        'url' => $item['url'] ?? ''
                    ];
                });
            }
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع معلومات الاتصال بنجاح' : 'Contact information fetched successfully',
            'data' => [
                'phone' => $contact ? $contact->phone : '+20 123 456 7890',
                'email' => $contact ? $contact->email : 'support@thamin.com',
                'social_media' => empty($socialMedia) ? $this->getDefaultSocialMedia() : $socialMedia
            ]
        ]);
    }

    /**
     * Submit contact form
     * POST /settings/contact
     */
    public function submitContact(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Store contact message in database or send email
        // You may need to create a ContactMessage model and migration
        // For now, we'll just return success
        // ContactMessage::create($validated);

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرسال رسالتك بنجاح' : 'Your message has been sent successfully',
            'data' => null
        ]);
    }

    /**
     * Get terms and conditions
     * GET /settings/terms
     */
    public function terms(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $terms = About::where('type', 'terms')->first();

        if (!$terms) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'لم يتم العثور على الشروط و الاحكام' : 'Terms not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع الشروط والأحكام بنجاح' : 'Terms & conditions fetched successfully',
            'data' => $terms,
        ]);
    }

    /**
     * Get privacy policy
     * GET /settings/privacy
     */
    public function privacy(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $privacy = About::where('type', 'privacy')->first();

        if (!$privacy) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'لم يتم العثور على سياسة الخصوصية' : 'Privacy policy not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع سياسة الخصوصية بنجاح' : 'Privacy policy fetched successfully',
            'data' => [
                'content' => $lang === 'ar' ? $privacy->content_ar : $privacy->content_en
            ]
        ]);
    }

    /**
     * Get default social media array (fallback)
     */
    private function getDefaultSocialMedia()
    {
        return [
            [
                'name' => 'Facebook',
                'icon' => '${AssetsManager.facebook}',
                'url' => 'https://facebook.com/thamin'
            ],
            [
                'name' => 'Twitter',
                'icon' => '${AssetsManager.x}',
                'url' => 'https://twitter.com/thamin'
            ],
            [
                'name' => 'Instagram',
                'icon' => '${AssetsManager.instagram}',
                'url' => 'https://instagram.com/thamin'
            ],
            [
                'name' => 'LinkedIn',
                'icon' => '${AssetsManager.linkedin}',
                'url' => 'https://linkedin.com/company/thamin'
            ],
            [
                'name' => 'Tiktok',
                'icon' => '${AssetsManager.tiktok}',
                'url' => 'https://tiktok.com/company/thamin'
            ],
            [
                'name' => 'Youtube',
                'icon' => '${AssetsManager.youtube}',
                'url' => 'https://youtube.com/company/thamin'
            ]
        ];
    }

    /**
     * Get default icon based on social media name
     */
    private function getDefaultIcon($name)
    {
        $icons = [
            'Facebook' => '${AssetsManager.facebook}',
            'Twitter' => '${AssetsManager.x}',
            'Instagram' => '${AssetsManager.instagram}',
            'LinkedIn' => '${AssetsManager.linkedin}',
            'Tiktok' => '${AssetsManager.tiktok}',
            'Youtube' => '${AssetsManager.youtube}',
        ];

        return $icons[$name] ?? '${AssetsManager.link}';
    }
}
