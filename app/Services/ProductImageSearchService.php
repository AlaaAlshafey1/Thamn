<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductImageSearchService
{
    /**
     * البحث عن صورة حقيقية عالية الجودة للمنتج بالاسم من مصادر الويب وقواعد البيانات المفتوحة
     * يدعم كافة الأقسام (سيارات، جوالات، مواشي، عقارات، ساعات، إلكترونيات، إلخ)
     *
     * @param array $queries قائمة بكلمات البحث مرتبة من الأكثر دقة إلى الأكثر عمومية
     * @param string|null $colorFilter لون السلعة لاختيار أفضل صورة متطابقة
     * @return string|null محتوى بايتس الصورة (Binary data) أو null إذا لم تتوفر
     */
    public function searchImage(array $queries, ?string $colorFilter = null): ?string
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept'     => 'application/json, text/plain, */*',
        ];

        foreach ($queries as $rawQuery) {
            $query = trim($rawQuery);
            if (empty($query) || mb_strlen($query) < 2) {
                continue;
            }

            try {
                $apiUrl = "https://commons.wikimedia.org/w/api.php";
                $response = Http::withHeaders($headers)
                    ->timeout(12)
                    ->get($apiUrl, [
                        'action'        => 'query',
                        'generator'     => 'search',
                        'gsrsearch'     => $query,
                        'gsrnamespace'  => 6, // File namespace
                        'gsrlimit'      => 8,
                        'prop'          => 'imageinfo',
                        'iiprop'        => 'url|mime|size',
                        'iiurlwidth'    => 1280, // جلب Thumbnail عالي الدقة وسريع التحميل
                        'format'        => 'json',
                    ]);

                if (!$response->successful()) {
                    continue;
                }

                $data = $response->json();
                $pages = $data['query']['pages'] ?? [];

                if (empty($pages)) {
                    continue;
                }

                // ترتيب النتائج إذا كان هناك تفضيل للون أو الموديل
                $candidates = [];
                foreach ($pages as $page) {
                    $title = $page['title'] ?? '';
                    $info = $page['imageinfo'][0] ?? null;
                    if (!$info) {
                        continue;
                    }

                    $mime = $info['mime'] ?? '';
                    $size = $info['size'] ?? 0;
                    $thumbUrl = $info['thumburl'] ?? ($info['url'] ?? null);

                    // استبعاد الأيقونات وملفات SVG والصور التوضيحية والصور الصغيرة جداً
                    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                        continue;
                    }

                    // استبعاد الرسوم البيانية والشعارات (Logos, Icons, Diagrams, Maps)
                    $titleLower = strtolower($title);
                    if (str_contains($titleLower, 'logo') || str_contains($titleLower, 'icon') || 
                        str_contains($titleLower, 'map') || str_contains($titleLower, 'diagram') ||
                        str_contains($titleLower, 'chart') || str_contains($titleLower, 'flag')) {
                        continue;
                    }

                    $score = 0;
                    if (!empty($colorFilter) && str_contains($titleLower, strtolower($colorFilter))) {
                        $score += 10;
                    }

                    $candidates[] = [
                        'score'    => $score,
                        'title'    => $title,
                        'thumbUrl' => $thumbUrl,
                        'origUrl'  => $info['url'] ?? null,
                    ];
                }

                // فرز حسب الأفضلية
                usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);

                foreach ($candidates as $cand) {
                    $targetUrl = $cand['thumbUrl'] ?: $cand['origUrl'];
                    if (!$targetUrl) {
                        continue;
                    }

                    $imgRes = Http::withHeaders($headers)
                        ->timeout(15)
                        ->get($targetUrl);

                    if ($imgRes->successful() && strlen($imgRes->body()) > 15000) {
                        Log::info("Found and fetched real product image from web for query '{$query}': {$cand['title']} (" . strlen($imgRes->body()) . " bytes)");
                        return $imgRes->body();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Web image search failed for query '{$query}': " . $e->getMessage());
            }
        }

        return null;
    }
}

