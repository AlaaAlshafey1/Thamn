<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSocialMediaSeeder extends Seeder
{
    /**
     * Populate the contacts table with Thamn's social media accounts
     * without overwriting any existing URLs or custom entries.
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

        $defaults = [
            [
                'key'     => 'instagram',
                'match'   => ['instagram', 'انستغرام', 'انستجرام'],
                'name'    => 'Instagram',
                'icon'    => 'fab fa-instagram',
                'url'     => 'https://www.instagram.com/thmmn.sa',
            ],
            [
                'key'     => 'youtube',
                'match'   => ['youtube', 'youtu.be', 'يوتيوب'],
                'name'    => 'YouTube',
                'icon'    => 'fab fa-youtube',
                'url'     => 'https://www.youtube.com/@thmmn',
            ],
            [
                'key'     => 'facebook',
                'match'   => ['facebook', 'fb.com', 'فيسبوك'],
                'name'    => 'Facebook',
                'icon'    => 'fab fa-facebook-f',
                'url'     => 'https://www.facebook.com/thmmn.sa',
            ],
            [
                'key'     => 'x',
                'match'   => ['x', 'x.com', 'twitter', 'تويتر', 'اكس'],
                'name'    => 'X',
                'icon'    => 'fab fa-twitter',
                'url'     => 'https://www.x.com/',
            ],
            [
                'key'     => 'snapchat',
                'match'   => ['snapchat', 'سناب'],
                'name'    => 'Snapchat',
                'icon'    => 'fab fa-snapchat-ghost',
                'url'     => 'https://www.snapchat.com/add/thmmn',
            ],
            [
                'key'     => 'tiktok',
                'match'   => ['tiktok', 'تيك توك', 'تيكتوك'],
                'name'    => 'TikTok',
                'icon'    => 'fab fa-tiktok',
                'url'     => 'https://www.tiktok.com/@thmmn',
            ],
        ];

        $usedIndexes = [];
        $merged      = [];

        foreach ($defaults as $def) {
            $foundIndex = null;

            foreach ($existing as $idx => $item) {
                $name = mb_strtolower(trim((string) ($item['name'] ?? '')));
                $url  = mb_strtolower(trim((string) ($item['url'] ?? '')));

                foreach ($def['match'] as $kw) {
                    if ($name === $kw || ($kw !== 'x' && ($name !== '' && str_contains($name, $kw) || ($url !== '' && str_contains($url, $kw))))) {
                        $foundIndex = $idx;
                        break 2;
                    }
                    if ($kw === 'x.com' && $url !== '' && str_contains($url, 'x.com')) {
                        $foundIndex = $idx;
                        break 2;
                    }
                }
            }

            if ($foundIndex !== null) {
                $usedIndexes[] = $foundIndex;
                $existingItem  = $existing[$foundIndex];
                $existingUrl   = trim((string) ($existingItem['url'] ?? ''));

                $merged[] = [
                    'name' => trim((string) ($existingItem['name'] ?? '')) ?: $def['name'],
                    'icon' => $def['icon'],
                    'url'  => $existingUrl !== '' ? $existingUrl : $def['url'],
                ];
            } else {
                $merged[] = [
                    'name' => $def['name'],
                    'icon' => $def['icon'],
                    'url'  => $def['url'],
                ];
            }
        }

        // Preserve any extra custom platforms already configured in the dashboard
        foreach ($existing as $idx => $item) {
            if (!in_array($idx, $usedIndexes, true) && trim((string) ($item['url'] ?? '')) !== '') {
                $merged[] = $item;
            }
        }

        $contact->social_media = $merged;
        $contact->save();

        $this->command?->info('Contact social_media seeded (' . count($merged) . ' platforms).');
    }
}
