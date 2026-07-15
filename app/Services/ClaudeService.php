<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected Client $http;
    protected string $key;
    protected string $model;

    // Anthropic API endpoint
    const API_URL = 'https://api.anthropic.com/v1/messages';
    const API_VERSION = '2023-06-01';

    public function __construct()
    {
        $this->http  = new Client(['timeout' => 180]);
        $this->key   = (string) env('ANTHROPIC_API_KEY');
        $this->model = env('ANTHROPIC_MODEL', 'claude-sonnet-4-5');
    }

    /**
     * تقييم سلعة عبر Claude Vision
     *
     * @param  string $prompt     الـ Prompt الكامل
     * @param  array  $imagePaths مسارات الصور المحلية على السيرفر
     * @return array  JSON مُفكَّك أو [] عند الخطأ
     */
    public function evaluateProduct(string $prompt, array $imagePaths = []): array
    {
        // ── بناء محتوى الرسالة (نص + صور) ──────────────────────────────────
        $content = [
            [
                'type' => 'text',
                'text' => $prompt,
            ]
        ];

        // أضف الصور (بصيغة base64 كما يتطلبها Claude)
        foreach (array_slice($imagePaths, 0, 5) as $path) {
            if (!file_exists($path)) continue;

            $ext      = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mimeMap  = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mediaType = $mimeMap[$ext] ?? 'image/jpeg';
            $b64Data   = base64_encode(file_get_contents($path));

            $content[] = [
                'type'   => 'image',
                'source' => [
                    'type'       => 'base64',
                    'media_type' => $mediaType,
                    'data'       => $b64Data,
                ],
            ];
        }

        // ── بناء system prompt قوي لنتائج أدق ──────────────────────────────
        $systemPrompt = <<<SYSTEM
أنت خبير معتمد في تثمين السلع المستعملة في السوق السعودي.
مهمتك الوحيدة: تقديم تقييم سعري دقيق وموضوعي بناءً على البيانات المدخلة والصور المرفقة.

قواعد صارمة لا يجوز كسرها:
- ردّك يجب أن يكون JSON نقياً فقط، لا أي نص قبله أو بعده، ولا code block.
- الأسعار بالريال السعودي، واقعية قابلة للمقارنة بمنصة حراج وموجز ونجم.
- reasoning باللغة العربية الفصحى، تحليلي مباشر لا يحتوي عبارات مثل "ربما" أو "يُحتمل".
- إذا رأيت صوراً، حللها بدقة وأثّر على السعر بناءً على الحالة الظاهرة.
- لا تُبالغ في التسعير أبداً — السعر العادل الواقعي هو الهدف.
SYSTEM;

        // ── استدعاء Claude API ───────────────────────────────────────────────
        try {
            $response = $this->http->post(self::API_URL, [
                'headers' => [
                    'x-api-key'         => $this->key,
                    'anthropic-version' => self::API_VERSION,
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => $this->model,
                    'max_tokens' => 2048,
                    'system'     => $systemPrompt,
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => $content,
                        ]
                    ],
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            // استخرج النص من الرد
            $raw = $body['content'][0]['text'] ?? null;

            if (!$raw) {
                Log::error('Claude: empty response', ['body' => $body]);
                return [];
            }

            // تنظيف الرد من أي code block لو Claude أضافها
            $raw = preg_replace('/```json\s*/i', '', $raw);
            $raw = preg_replace('/```\s*/i', '', $raw);
            $raw = trim($raw);

            $json = json_decode($raw, true);

            if (!is_array($json)) {
                Log::error('Claude: invalid JSON in response', ['raw' => $raw]);
                return [];
            }

            Log::info('Claude evaluation success', [
                'order_images' => count($imagePaths),
                'recommended'  => $json['recommended_price'] ?? null,
                'confidence'   => $json['confidence'] ?? null,
            ]);

            return $json;

        } catch (\Throwable $e) {
            Log::error('Claude API failed', [
                'message' => $e->getMessage(),
                'model'   => $this->model,
            ]);

            return [];
        }
    }

    /**
     * توليد الصور — Claude لا يدعمها, نُفوّض لـ OpenAI DALL-E
     */
    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        return app(OpenAIService::class)->generateImage($prompt, $size);
    }
}
