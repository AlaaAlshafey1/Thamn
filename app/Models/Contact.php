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
     * Subscribed platforms shown in the email footer, in display order.
     */
    private const EMAIL_ALLOWED_PLATFORMS = ['tiktok', 'instagram', 'x', 'whatsapp', 'youtube'];

    /**
     * Platform detection rules, checked in order.
     * Each rule: platform key => keywords (matched against name, then url, then icon class).
     */
    private const SOCIAL_PLATFORMS = [
        'instagram' => ['instagram', 'instgram', 'انستغرام', 'انستجرام', 'إنستغرام', 'إنستجرام'],
        'youtube'   => ['youtube', 'youtu.be', 'يوتيوب'],
        'facebook'  => ['facebook', 'fb.com', 'fb.me', 'فيسبوك', 'فيس بوك'],
        'whatsapp'  => ['whatsapp', 'wa.me', 'واتساب', 'واتس اب', 'واتس'],
        'telegram'  => ['telegram', 't.me', 'تيليجرام', 'تليجرام', 'تيليغرام'],
        'snapchat'  => ['snapchat', 'سناب'],
        'tiktok'    => ['tiktok', 'تيك توك', 'تيكتوك', 'تك توك'],
        'linkedin'  => ['linkedin', 'لينكد'],
        'pinterest' => ['pinterest', 'بنترست'],
        'twitter'   => ['twitter', 'تويتر'],
    ];

    /**
     * Social links for the e-mail footer, read from the dashboard (Contacts > social media).
     * Keeps only the 5 subscribed platforms, fixes invalid/Google WhatsApp links, and orders them cleanly.
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
        $byPlatform = [];

        foreach ($items as $item) {
            $url      = trim((string) ($item['url'] ?? ''));
            $name     = trim((string) ($item['name'] ?? ''));
            $platform = static::detectPlatform($name, $url, (string) ($item['icon'] ?? ''));
            if ($platform === 'twitter') {
                $platform = 'x';
            }
            if (!in_array($platform, self::EMAIL_ALLOWED_PLATFORMS, true)) {
                continue;
            }

            if ($platform === 'whatsapp') {
                $url = static::normalizeWhatsappUrl($url, $contact?->phone);
            } elseif ($url === '' || $url === '#') {
                continue;
            } elseif (!preg_match('#^(https?://|mailto:|tel:)#i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }

            if (isset($byPlatform[$platform])) {
                continue;
            }

            $byPlatform[$platform] = [
                'name'      => $name !== '' ? $name : ucfirst($platform),
                'url'       => $url,
                'platform'  => $platform,
                'icon'      => $assetBase . '/assets/emails/social/' . $platform . '.png',
                'icon_path' => public_path('assets/emails/social/' . $platform . '.png'),
            ];
        }

        $ordered = [];
        foreach (self::EMAIL_ALLOWED_PLATFORMS as $platform) {
            if (isset($byPlatform[$platform])) {
                $ordered[] = $byPlatform[$platform];
            }
        }

        return $ordered;
    }

    /**
     * Ensures WhatsApp links resolve to a real wa.me link rather than Google or a broken URL.
     */
    public static function normalizeWhatsappUrl(?string $rawUrl, ?string $fallbackPhone): string
    {
        $raw   = trim((string) $rawUrl);
        $lower = mb_strtolower($raw);

        if ($raw !== '' && !str_contains($lower, 'google.com') && (str_contains($lower, 'wa.me/') || str_contains($lower, 'whatsapp.com'))) {
            return preg_match('#^https?://#i', $raw) ? $raw : 'https://' . ltrim($raw, '/');
        }

        $onlyDigitsCandidate = preg_replace('#^https?://#i', '', $raw);
        $digits = preg_match('/^[+\d\s\-()]+$/', $onlyDigitsCandidate)
            ? preg_replace('/\D/', '', $onlyDigitsCandidate)
            : '';

        if (strlen($digits) < 9) {
            $digits = preg_replace('/\D/', '', (string) $fallbackPhone);
        }
        if ($digits === '') {
            $digits = '966555212555';
        } elseif (str_starts_with($digits, '05') && strlen($digits) === 10) {
            $digits = '966' . substr($digits, 1);
        } elseif (str_starts_with($digits, '5') && strlen($digits) === 9) {
            $digits = '966' . $digits;
        }

        return 'https://wa.me/' . $digits;
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
