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

        Log::error('OpenAI Image Generation failed across all models', ['prompt' => $prompt]);
        return null;
    }
}
