<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSocialMediaSeeder extends Seeder
{
    /**
     * Populate contacts.social_media strictly with Thamn's 5 subscribed platforms:
     * TikTok, Instagram, X, WhatsApp, YouTube.
     */
    public function run(): void
    {
        $contact = Contact::first();

        if (!$contact) {
            $contact = Contact::create([
                'phone'        => '0555212555',
                'email'        => 'info@thamn.sa',
                'social_media' => [],
            ]);
        }

        $existing = is_string($contact->social_media)
            ? (json_decode($contact->social_media, true) ?? [])
            : ($contact->social_media ?? []);

        if (!is_array($existing)) {
            $existing = [];
        }

        $whatsappFallback = Contact::normalizeWhatsappUrl(null, $contact->phone);

        $platforms = [
            [
                'key'   => 'tiktok',
                'match' => ['tiktok', 'تيك توك', 'تيكتوك', 'تك توك'],
                'name'  => 'TikTok',
                'icon'  => 'fab fa-tiktok',
                'url'   => 'https://www.tiktok.com/@thmmn',
            ],
            [
                'key'   => 'instagram',
                'match' => ['instagram', 'انستغرام', 'انستجرام', 'إنستغرام', 'إنستجرام'],
                'name'  => 'Instagram',
                'icon'  => 'fab fa-instagram',
                'url'   => 'https://www.instagram.com/thmmn.sa',
            ],
            [
                'key'   => 'x',
                'match' => ['x', 'x.com', 'twitter', 'تويتر', 'اكس', 'إكس'],
                'name'  => 'X',
                'icon'  => 'fab fa-twitter',
                'url'   => 'https://www.x.com/',
            ],
            [
                'key'   => 'whatsapp',
                'match' => ['whatsapp', 'wa.me', 'واتساب', 'واتس اب', 'واتس'],
                'name'  => 'WhatsApp',
                'icon'  => 'fab fa-whatsapp',
                'url'   => $whatsappFallback,
            ],
            [
                'key'   => 'youtube',
                'match' => ['youtube', 'youtu.be', 'يوتيوب'],
                'name'  => 'YouTube',
                'icon'  => 'fab fa-youtube',
                'url'   => 'https://www.youtube.com/@thmmn',
            ],
        ];

        $seeded = [];

        foreach ($platforms as $def) {
            $existingItem = null;

            foreach ($existing as $item) {
                $name = mb_strtolower(trim((string) ($item['name'] ?? '')));
                $url  = mb_strtolower(trim((string) ($item['url'] ?? '')));

                foreach ($def['match'] as $kw) {
                    if ($name === $kw) {
                        $existingItem = $item;
                        break 2;
                    }
                    if (!in_array($kw, ['x', 'x.com'], true) && (($name !== '' && str_contains($name, $kw)) || ($url !== '' && str_contains($url, $kw)))) {
                        $existingItem = $item;
                        break 2;
                    }
                    if ($kw === 'x.com' && $url !== '' && str_contains($url, 'x.com')) {
                        $existingItem = $item;
                        break 2;
                    }
                }
            }

            $existingUrl = trim((string) ($existingItem['url'] ?? ''));

            if ($def['key'] === 'whatsapp') {
                $resolvedUrl = Contact::normalizeWhatsappUrl($existingUrl, $contact->phone);
            } else {
                $resolvedUrl = $existingUrl !== '' ? $existingUrl : $def['url'];
            }

            $seeded[] = [
                'name' => $def['name'],
                'icon' => $def['icon'],
                'url'  => $resolvedUrl,
            ];
        }

        $contact->social_media = $seeded;
        $contact->save();

        $this->command?->info('Contact social_media seeded (' . count($seeded) . ' platforms).');
    }
}
