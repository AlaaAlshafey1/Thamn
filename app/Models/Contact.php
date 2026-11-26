<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'social_media',
    ];

    protected $casts = [
        'social_media' => 'array',
    ];

    /**
     * Platform detection rules, checked in order.
     * Each rule: platform key => keywords (matched against name, then url, then icon class).
     */
    private const SOCIAL_PLATFORMS = [
        'instagram' => ['instagram', 'انستغرام', 'انستجرام', 'إنستغرام', 'إنستجرام'],
        'youtube'   => ['youtube', 'youtu.be', 'يوتيوب'],
        'facebook'  => ['facebook', 'fb.com', 'fb.me', 'فيسبوك', 'فيس بوك'],
        'whatsapp'  => ['whatsapp', 'wa.me', 'واتساب', 'واتس اب'],
        'telegram'  => ['telegram', 't.me', 'تيليجرام', 'تليجرام', 'تيليغرام'],
        'snapchat'  => ['snapchat', 'سناب'],
        'tiktok'    => ['tiktok', 'تيك توك', 'تيكتوك'],
        'linkedin'  => ['linkedin', 'لينكد'],
        'pinterest' => ['pinterest', 'بنترست'],
        'twitter'   => ['twitter', 'تويتر'],
    ];

    /**
     * Social links for the e-mail footer, read from the dashboard (Contacts > social media).
     * Returns only entries that have a URL, each with a hosted brand icon.
     *
     * @return array<int, array{name:string, url:string, platform:string, icon:string, icon_path:string}>
     */
    public static function emailSocialLinks(string $assetBase): array
    {
        $contact = static::first();
        $items   = $contact?->social_media;

        if (is_string($items)) {
            $items = json_decode($items, true);
        }
        if (!is_array($items)) {
            return [];
        }

        $assetBase = rtrim($assetBase, '/');
        $links     = [];

        foreach ($items as $item) {
            $url = trim((string) ($item['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            if (!preg_match('#^(https?://|mailto:|tel:)#i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }

            $name     = trim((string) ($item['name'] ?? ''));
            $platform = static::detectPlatform($name, $url, (string) ($item['icon'] ?? ''));

            $links[] = [
                'name'      => $name !== '' ? $name : ucfirst($platform),
                'url'       => $url,
                'platform'  => $platform,
                'icon'      => $assetBase . '/assets/emails/social/' . $platform . '.png',
                'icon_path' => public_path('assets/emails/social/' . $platform . '.png'),
            ];
        }

        return $links;
    }

    /**
     * Name wins over URL, URL wins over the icon class (the icon field is often left at a default).
     */
    private static function detectPlatform(string $name, string $url, string $iconClass): string
    {
        $name = mb_strtolower($name);
        $url  = mb_strtolower($url);

        // "X" / x.com
        if (in_array($name, ['x', 'x.com', '𝕏', 'اكس', 'إكس'], true)) {
            return 'x';
        }

        $scan = function (string $haystack): ?string {
            if ($haystack === '') {
                return null;
            }
            foreach (self::SOCIAL_PLATFORMS as $platform => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($haystack, $keyword)) {
                        return $platform;
                    }
                }
            }
            return null;
        };

        if ($platform = $scan($name)) {
            return $platform;
        }
        if ($platform = $scan($url)) {
            return $platform;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);
        if ($host === 'x.com' || $host === 'www.x.com') {
            return 'x';
        }

        // The icon class is the last resort (it is frequently left at a default value)
        if ($platform = $scan(mb_strtolower($iconClass))) {
            return $platform;
        }

        return 'link';
    }
}
