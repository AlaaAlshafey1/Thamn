<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected Client $http;
    protected string $base;
    protected string $key;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 120]);

        $this->base = rtrim(
            config('services.openai.base') ?: env('OPENAI_API_BASE', 'https://api.openai.com/v1/'),
            '/'
        );

        $this->key = (string) (
            config('services.openai.key')
            ?: config('services.openai.OPENAI_API_KEY')
            ?: env('OPENAI_API_KEY')
            ?: env('OPENAI_KEY')
        );
    }

    /**
     * Chat Completion (عام)
     */
    public function chat(array $messages, string $model = 'gpt-4o-mini'): array
    {
        try {
            $res = $this->http->post($this->base . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'response_format' => [
                        'type' => 'json_object'
                    ],
                ],
            ]);

            return json_decode((string) $res->getBody(), true);

        } catch (\Throwable $e) {
            Log::error('OpenAI Chat API failed', [
                'message' => $e->getMessage(),
                'model' => $model,
                'messages' => $messages,
            ]);

            return [];
        }
    }

    /**
     * ===============================
     * تقييم سلعة (AI Valuation)
     * ===============================
     */
    public function evaluateProduct(string $prompt, array $imagePaths = []): array
    {
        $content = [
            [
                'type' => 'text',
                'text' => $prompt
            ]
        ];

        // Add images if provided
        foreach (array_slice($imagePaths, 0, 5) as $path) {
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = base64_encode(file_get_contents($path));
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:image/{$type};base64,{$data}"
                    ]
                ];
            }
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'أنت خبير محترف في تثمين السلع وبالأخص السوق السعودي. تستخدم الصور المرفقة لتقييم حالة المنتج وجودته وندرته.'
            ],
            [
                'role' => 'user',
                'content' => $content
            ],
        ];

        $response = $this->chat($messages, 'gpt-4o-mini');

        $raw = $response['choices'][0]['message']['content'] ?? '{}';

        $json = json_decode($raw, true);

        if (!is_array($json)) {
            Log::error('AI valuation invalid JSON', [
                'response' => $raw
            ]);

            return [];
        }

        return $json;
    }
    /**
     * تحليل إجابات الأسئلة واستخراج بيانات البحث عن الصورة واسم المنتج والبرومبت لأي تصنيف ديناميكياً بواسطة GPT-4o
     */
    public function resolveProductImageDetails(string $answersText, string $category = '', string $objectionText = ''): array
    {
        $system = "You are an expert product identification and catalog specialist. Your job is to analyze product questionnaire answers across ANY category (smartphones, vehicles, livestock/animals, real estate, luxury watches, electronics, equipment, etc.) and return a structured JSON.";

        $user = <<<PROMPT
Category: {$category}
Product Questionnaire & Inspection Details:
{$answersText}
{$objectionText}

Analyze all the answers above. Output ONLY a valid JSON object with these exact keys:
{
  "product_title_en": "Precise make and model/breed/property name in English",
  "product_title_ar": "الاسم الدقيق بالعربية",
  "search_queries": [
    "Most specific search query for an authentic photograph of this exact item",
    "Secondary search query",
    "Broader fallback search query"
  ],
  "color": "Color/finish in English (e.g. Silver, Black, Desert Titanium, White, etc.) or null",
  "prompt": "High-end commercial catalog studio photograph prompt in English for DALL-E/FLUX fallback. Pure seamless studio white background (#FFFFFF) for objects/vehicles/phones/watches, or clean authentic setting for livestock/real estate. Strictly NO humans, NO women, NO faces, NO packaging boxes, NO cables, NO text, NO watermarks."
}
PROMPT;

        try {
            $res = $this->http->post($this->base . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $user]
                    ],
                    'temperature' => 0.3,
                ],
                'timeout' => 20,
            ]);

            $body = json_decode((string) $res->getBody(), true);
            $text = trim($body['choices'][0]['message']['content'] ?? '');
            if (!empty($text)) {
                $clean = preg_replace('/```json\s*/i', '', $text);
                $clean = preg_replace('/```\s*/i', '', $clean);
                $data = json_decode(trim($clean), true);
                if (is_array($data) && !empty($data['search_queries'])) {
                    return $data;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI resolveProductImageDetails failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * توليد برومبت إنجليزي فائق الدقة للصورة بواسطة ذكاء GPT-4o بناءً على كافة أسئلة وأجوبة الطلب
     */
    public function generateImagePromptFromDetails(string $answersText, string $category = '', string $objectionText = ''): string
    {
        $system = "You are an expert commercial product catalog art director. Your job is to analyze full product inspection answers and generate a single, highly detailed, photorealistic studio product photography prompt in English for studio image generation.";

        $user = <<<PROMPT
Category: {$category}
Product Questionnaire & Inspection Details:
{$answersText}
{$objectionText}

Based STRICTLY on the user's inspection answers and objection details above, generate a concise, highly specific commercial studio product photograph prompt in English:
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
            $res = $this->http->post($this->base . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $user]
                    ],
                    'temperature' => 0.4,
                ],
                'timeout' => 30,
            ]);

            $body = json_decode((string) $res->getBody(), true);
            $text = trim($body['choices'][0]['message']['content'] ?? '');
            if (!empty($text)) {
                return $text;
            }
        } catch (\Throwable $e) {
            Log::error('OpenAI generateImagePromptFromDetails failed: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * ===============================
     * توليد صور (OpenAI Image API)
     * ===============================
     */
    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        $preferredModel = config('services.openai.image_model') ?: env('OPENAI_IMAGE_MODEL', 'chatgpt-image-latest');

        $models = array_values(array_unique(array_filter([
            $preferredModel,
            'chatgpt-image-latest',
            'gpt-image-2',
            'gpt-image-1.5',
            'gpt-image-1-mini',
            'dall-e-3',
            'dall-e-2',
        ])));

        foreach ($models as $model) {
            try {
                $payload = [
                    'model'  => $model,
                    'prompt' => $prompt,
                ];

                if (str_starts_with($model, 'dall-e')) {
                    $payload['n'] = 1;
                    $payload['size'] = $size;
                    $payload['response_format'] = 'b64_json';
                    if ($model === 'dall-e-3') {
                        $payload['quality'] = 'standard';
                    }
                }

                $res = $this->http->post($this->base . '/images/generations', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->key,
                        'Content-Type'  => 'application/json',
                    ],
                    'json'    => $payload,
                    'timeout' => 90,
                ]);

                $json = json_decode((string) $res->getBody(), true);

                if (!empty($json['data'][0]['b64_json'])) {
                    return 'data:image/png;base64,' . $json['data'][0]['b64_json'];
                } elseif (!empty($json['data'][0]['url'])) {
                    return $json['data'][0]['url'];
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI Image Generation model {$model} failed: " . $e->getMessage());
                continue;
            }
        }

        Log::warning('OpenAI Image Generation failed across all models, falling back to high-resolution studio image engine (FLUX)', ['prompt' => $prompt]);

        // محرك بديل فائق الجودة والسرعة (FLUX) لضمان عدم توقف توليد الصور أبداً في حال مشاكل الصلاحيات أو الرصيد في OpenAI
        try {
            $fluxUrl = 'https://image.pollinations.ai/prompt/' . urlencode($prompt) . '?model=flux&width=1024&height=1024&nologo=true';
            $imgRes = \Illuminate\Support\Facades\Http::timeout(30)->get($fluxUrl);
            if ($imgRes->successful() && strlen($imgRes->body()) > 5000) {
                Log::info('Successfully generated fallback image via FLUX engine', ['bytes' => strlen($imgRes->body())]);
                return 'data:image/png;base64,' . base64_encode($imgRes->body());
            }
        } catch (\Throwable $e) {
            Log::error('Studio image fallback engine (FLUX) failed: ' . $e->getMessage());
        }

        return null;
    }
}
