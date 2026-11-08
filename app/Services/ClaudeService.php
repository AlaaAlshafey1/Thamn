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
أنت خبير ومثمّن معتمد رفيع المستوى في تثمين السلع المستعملة والجديدة في السوق السعودي (حسب معايير الهيئة السعودية للمقيّمين المعتمدين - تقييم).
مهمتك الأساسية: تقديم تقييم سعري دقيق وموضوعي يعكس صفقات البيع والشراء الحقيقية في المنصات السعودية الرائدة (حراج، مستعمل وجديد، جرير، إكسترا، أمازون السعودية، نون، سيارة، موجز).

قواعد صارمة وإلزامية لا يجوز كسرها أبداً:
1. الرد يجب أن يكون بصيغة JSON نقياً فقط، دون أي نص قبله أو بعده، ودون تغليف Markdown code block.
2. الأسعار بالريال السعودي (SAR)، واقعية ومنطقية ومطابقة لحركة السوق المحلي الفعلي.
3. قاعدة الفجوة الرياضية الإلزامية:
   - احسب recommended_price أولاً.
   - ثم min_price = round(recommended_price × 0.93)
   - ثم max_price = round(recommended_price × 1.07)
   - الفجوة بين min و max لا تتجاوز 14% دائماً.
4. قواعد التعامل مع اعتراضات العملاء وإعادة التثمين (إلزامية قطعية):
   - إذا كان هذا طلب إعادة تثمين وكان سبب الاعتراض أن السعر السابق "أعلى من المتوقع" أو "مرتفع": يجب حتماً وبشكل قاطع أن يكون recommended_price أقل من السعر السابق، مع تخفيض منطقي للأخذ بأسعار صفقات البيع السريع ("السوم الناجز") والحد الأدنى في حراج ومستعمل. يُعد رفع السعر هنا خطأً فادحاً غير مقبول.
   - إذا كان سبب الاعتراض أن السعر السابق "أقل من المتوقع" أو "بخس": يجب أن يكون recommended_price أعلى من السعر السابق لإنصاف العميل، مع افتراض أفضلية النظافة واكتمال الملحقات ودون تجاوز سقف السلعة جديدة في جرير/أمازون.
5. قواعد الـ confidence:
   - بين 0.70 و 0.95 بناءً على اكتمال الصور والبيانات؛ لا يُقبل أبداً أي رقم أقل من 0.60.
6. أسلوب reasoning:
   - باللغة العربية الفصحى، تحليلي مهني ومباشر، يصف السلعة وحالتها وقيمتها السوقية العادلة.
   - ممنوع ذكر كلمة "اعتراض" في النص، وممنوع ذكر أسماء المنصات باسمها التجاري (مثل حراج أو جرير) في نص التقرير، بل استخدم عبارات مثل: "وفق متوسط أسعار السوق السعودي الحالي" أو "بناءً على صفقات التداول المنفذة للسلع المماثلة".
7. قاعدة التصفية الزمنية في المنصات ومواكبة العصر (إلزامية):
   - التثمين يرتكز حصراً على صفقات البيع والشراء المنفذة في كبرى المنصات السعودية المفلترة لآخر شهر (30 يوماً فقط) من تاريخ تنفيذ الطلب المحدد في البرومبت.
   - يُمنع تماماً الاستناد لأسعار أو صفقات قديمة تزيد عن شهر مضى.
   - يجب مواكبة العصر والأخذ بالمعطيات الفنية الصحيحة للموديل المعاصر دون خلطه بموديلات قديمة.
SYSTEM;

        // ── استدعاء Claude API ───────────────────────────────────────────────
        $preferredModel = env('ANTHROPIC_MODEL', 'claude-3-7-sonnet-20250219');
        if ($preferredModel === 'claude-sonnet-4-5') {
            $preferredModel = 'claude-3-7-sonnet-20250219';
        }

        $models = array_values(array_unique(array_filter([
            $preferredModel,
            'claude-3-7-sonnet-20250219',
            'claude-3-5-sonnet-latest',
            'claude-3-5-sonnet-20241022',
        ])));

        foreach ($models as $currentModel) {
            try {
                $response = $this->http->post(self::API_URL, [
                    'headers' => [
                        'x-api-key'         => $this->key,
                        'anthropic-version' => self::API_VERSION,
                        'content-type'      => 'application/json',
                    ],
                    'json' => [
                        'model'      => $currentModel,
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
                $raw = $body['content'][0]['text'] ?? null;

                if (!$raw) {
                    Log::warning("Claude model {$currentModel} returned empty response");
                    continue;
                }

                // تنظيف الرد من أي code block لو Claude أضافها
                $raw = preg_replace('/```json\s*/i', '', $raw);
                $raw = preg_replace('/```\s*/i', '', $raw);
                $raw = trim($raw);

                $json = json_decode($raw, true);

                if (!is_array($json)) {
                    Log::warning("Claude model {$currentModel} returned invalid JSON: {$raw}");
                    continue;
                }

                Log::info('Claude evaluation success', [
                    'model'        => $currentModel,
                    'order_images' => count($imagePaths),
                    'recommended'  => $json['recommended_price'] ?? null,
                    'confidence'   => $json['confidence'] ?? null,
                ]);

                return $json;

            } catch (\Throwable $e) {
                Log::warning("Claude model {$currentModel} failed: " . $e->getMessage());
                continue;
            }
        }

        Log::error('Claude API failed across all models');
        return [];
    }

    /**
     * توليد برومبت إنجليزي فائق الدقة للصورة بواسطة ذكاء Claude بناءً على كافة أسئلة وأجوبة الطلب
     */
    public function generateImagePromptFromDetails(string $answersText, string $category = '', string $objectionText = ''): string
    {
        $system = "You are an expert commercial product catalog art director. Your job is to analyze full product inspection answers and generate a single, highly detailed, photorealistic studio product photography prompt in English for DALL-E 3 image generation.";

        $user = <<<PROMPT
Category: {$category}
Product Questionnaire & Inspection Details:
{$answersText}
{$objectionText}

Based STRICTLY on the user's inspection answers and objection details above, generate a concise, highly specific commercial studio product photograph prompt in English for DALL-E 3:
1. Deeply analyze all answers to extract the exact product:
   - Accurately determine the exact brand, line, specific model/edition, production year, and generation.
   - Extract the exact manufacturing materials specified in the answers (e.g., aluminum, titanium, stainless steel, ceramic, glass, etc.).
   - Extract the exact color, shade, and surface finish (e.g., Deep Blue, Matte, Glossy, etc.).
   - Factor in all hardware details and user-specified features (e.g., number of camera lenses, camera layout, physical buttons, display style).
2. Depict the authentic, genuine real-world industrial design and hardware architecture of this exact model and generation (including its authentic camera module arrangement, chassis shape, bezels, and design signature).
3. The item must be presented as a single clean, premium standalone product centered on a seamless, pure studio white background (#FFFFFF) with high-end commercial catalog studio lighting and soft subtle contact shadows. (Do NOT generate two mismatching devices side-by-side).
4. STRICTLY NO packaging boxes, NO cardboard boxes, NO charging cables, NO power adapters, NO accessories clutter, NO text, NO watermarks.

Output ONLY the raw English prompt string, with no quotes, no markdown, and no introductory or concluding remarks.
PROMPT;

        try {
            $models = [
                'claude-3-7-sonnet-20250219',
                'claude-3-5-sonnet-latest',
                'claude-3-5-sonnet-20241022',
                'claude-3-5-haiku-latest',
            ];

            foreach ($models as $model) {
                try {
                    $response = $this->http->post(self::API_URL, [
                        'headers' => [
                            'x-api-key'         => $this->key,
                            'anthropic-version' => self::API_VERSION,
                            'content-type'      => 'application/json',
                        ],
                        'json' => [
                            'model'      => $model,
                            'max_tokens' => 500,
                            'system'     => $system,
                            'messages'   => [
                                ['role' => 'user', 'content' => $user]
                            ],
                        ],
                        'timeout' => 30,
                    ]);

                    $body = json_decode((string) $response->getBody(), true);
                    $text = trim($body['content'][0]['text'] ?? '');
                    if (!empty($text)) {
                        return $text;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        } catch (\Throwable $e) {
            Log::error('generateImagePromptFromDetails failed: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * توليد الصور — Claude لا يدعمها, نُفوّض لـ OpenAI DALL-E
     */
    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        return app(OpenAIService::class)->generateImage($prompt, $size);
    }
}
