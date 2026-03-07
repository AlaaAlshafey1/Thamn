<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function privacy(Request $request)
    {
        $page = About::where('type', 'privacy')->first();
        $title = lang('سياسة الخصوصية', 'Privacy Policy', $request);
        return view('public.page', compact('page', 'title'));
    }

    public function terms(Request $request)
    {
        $page = About::where('type', 'terms')->first();
        $title = lang('الشروط والأحكام', 'Terms & Conditions', $request);
        return view('public.page', compact('page', 'title'));
    }

    public function about(Request $request)
    {
        $page = About::where('type', 'about')->first();
        $title = lang('عن ثمن', 'About Us', $request);
        return view('public.page', compact('page', 'title'));
    }

    public function contact(Request $request)
    {
        $contactInfo = Contact::first();
        $title = lang('اتصل بنا', 'Contact Us', $request);

        // Process social media like the API method
        $socialMedia = [];
        if ($contactInfo && is_array($contactInfo->social_media)) {
            $socials = $contactInfo->social_media;
            if (count($socials) > 0) {
                $socialMedia = collect($socials)->map(function ($item) {
                    return [
                        'name' => $item['name'] ?? '',
                        'icon' => $item['icon'] ?? $this->getDefaultIcon($item['name'] ?? ''),
                        'url' => $item['url'] ?? ''
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
