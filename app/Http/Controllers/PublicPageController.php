<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Banner;
use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\HomeStep;
use App\Models\Intro;
use App\Models\Order;
use App\Models\TermCondition;
use App\Models\User;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    protected function setupLocale(Request $request): string
    {
        $lang = $request->get('lang', session('locale', 'ar'));
        if (!in_array($lang, ['ar', 'en'])) {
            $lang = 'ar';
        }
        app()->setLocale($lang);
        return $lang;
    }

    public function index(Request $request)
    {
        $lang = $this->setupLocale($request);

        // 1. Promotional Banners for Popup Modal
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();

        // 2. Hero Section (from Welcome Intro if available)
        $welcomeIntro = Intro::where('page', 'welcome')->where('is_active', true)->first();

        // 3. Features & How It Works Sections (from HomeSteps)
        $featureSteps = HomeStep::where('type', 'check')->where('is_active', true)->orderBy('sort_order')->first();
        $workSteps = HomeStep::where('type', 'steps')->where('is_active', true)->orderBy('sort_order')->first();

        // 4. FAQs
        $faqs = Faq::all();

        // 5. Contact Info & Social Media
        $contactInfo = Contact::first();
        $socialMedia = [];
        if ($contactInfo && $contactInfo->social_media) {
            $socials = is_string($contactInfo->social_media)
                ? json_decode($contactInfo->social_media, true)
                : $contactInfo->social_media;

            if (is_array($socials) && count($socials) > 0) {
                $socialMedia = collect($socials)->filter(function ($item) {
                    return !empty($item['url']) || !empty($item['name']);
                })->map(function ($item) {
                    $name = $item['name'] ?? '';
                    $icon = $item['icon'] ?? '';
                    if (empty($icon) && !empty($name)) {
                        $icon = $this->getDefaultIcon($name);
                    }
                    if (empty($icon)) {
                        $icon = 'fas fa-link';
                    }
                    return [
                        'name' => $name,
                        'icon' => $icon,
                        'url' => $item['url'] ?? '#'
                    ];
                });
            }
        }
        if (empty($socialMedia)) {
            $socialMedia = $this->getDefaultSocialMedia();
        }

        // 6. Statistics
        $expertsCount = max(User::role('expert')->count(), 45);
        $ordersCount = max(Order::count(), 15);

        return view('public.home', compact(
            'banners',
            'welcomeIntro',
            'featureSteps',
            'workSteps',
            'faqs',
            'contactInfo',
            'socialMedia',
            'expertsCount',
            'ordersCount',
            'lang'
        ));
    }

    public function privacy(Request $request)
    {
        $lang = $this->setupLocale($request);
        $page = About::where('type', 'privacy')->first();
        $title = $lang === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy';
        return view('public.page', compact('page', 'title'));
    }

    public function terms(Request $request)
    {
        $lang = $this->setupLocale($request);
        $page = About::where('type', 'terms')->first();
        $title = $lang === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions';
        return view('public.page', compact('page', 'title'));
    }

    public function about(Request $request)
    {
        $lang = $this->setupLocale($request);
        $page = About::where('type', 'about')->first();
        $title = $lang === 'ar' ? 'عن ثمن' : 'About Us';
        return view('public.page', compact('page', 'title'));
    }

    public function contact(Request $request)
    {
        $lang = $this->setupLocale($request);
        $contactInfo = Contact::first();
        $title = $lang === 'ar' ? 'اتصل بنا' : 'Contact Us';

        // Process social media like the API method
        $socialMedia = [];
        if ($contactInfo && $contactInfo->social_media) {
            $socials = is_string($contactInfo->social_media)
                ? json_decode($contactInfo->social_media, true)
                : $contactInfo->social_media;

            if (is_array($socials) && count($socials) > 0) {
                $socialMedia = collect($socials)->filter(function ($item) {
                    return !empty($item['url']) || !empty($item['name']);
                })->map(function ($item) {
                    $name = $item['name'] ?? '';
                    $icon = $item['icon'] ?? '';

                    if (empty($icon) && !empty($name)) {
                        $icon = $this->getDefaultIcon($name);
                    }

                    if (empty($icon)) {
                        $icon = 'fas fa-link';
                    }

                    return [
                        'name' => $name,
                        'icon' => $icon,
                        'url' => $item['url'] ?? '#'
                    ];
                });
            }
        }

        if (empty($socialMedia)) {
            $socialMedia = $this->getDefaultSocialMedia();
        }

        return view('public.contact', compact('contactInfo', 'title', 'socialMedia'));
    }

    private function getDefaultIcon($name)
    {
        $name = strtolower($name);
        if (str_contains($name, 'facebook'))
            return 'fab fa-facebook';
        if (str_contains($name, 'twitter') || str_contains($name, 'x'))
            return 'fab fa-x-twitter';
        if (str_contains($name, 'instagram'))
            return 'fab fa-instagram';
        if (str_contains($name, 'linkedin'))
            return 'fab fa-linkedin-in';
        if (str_contains($name, 'whatsapp'))
            return 'fab fa-whatsapp';
        if (str_contains($name, 'snapchat'))
            return 'fab fa-snapchat';
        if (str_contains($name, 'tiktok'))
            return 'fab fa-tiktok';
        return 'fas fa-link';
    }

    private function getDefaultSocialMedia()
    {
        return [
            ['name' => 'X', 'icon' => 'fab fa-x-twitter', 'url' => '#'],
            ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'url' => '#'],
            ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'url' => '#'],
        ];
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create($request->all());

        return back()->with('success', lang('تم إرسال رسالتك بنجاح!', 'Your message has been sent successfully!', $request));
    }
}
