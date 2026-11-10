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
     * ===============================
     * توليد صور (DALL-E)
     * ===============================
     */
    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        $preferredModel = config('services.openai.image_model') ?: env('OPENAI_IMAGE_MODEL', 'dall-e-3');

        $models = array_values(array_unique(array_filter([
            $preferredModel,
            'dall-e-3',
            'dall-e-2',
        ])));

        foreach ($models as $model) {
            try {
                $payload = [
                    'model'           => $model,
                    'prompt'          => $prompt,
                    'n'               => 1,
                    'size'            => $size,
                    'response_format' => 'b64_json',
                ];

                if ($model === 'dall-e-3') {
                    $payload['quality'] = 'standard';
                }

                $res = $this->http->post($this->base . '/images/generations', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->key,
                        'Content-Type'  => 'application/json',
                    ],
                    'json'    => $payload,
                    'timeout' => 60,
                ]);

                $json = json_decode((string) $res->getBody(), true);

                if (!empty($json['data'][0]['b64_json'])) {
                    return 'data:image/png;base64,' . $json['data'][0]['b64_json'];
                } elseif (!empty($json['data'][0]['url'])) {
                    return $json['data'][0]['url'];
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI Image Generation model {$model} failed (b64_json): " . $e->getMessage());

                // محاولة بديلة بدون response_format (لإرجاع URL مباشرة)
                try {
                    $fallbackPayload = [
                        'model'  => $model,
                        'prompt' => $prompt,
                        'n'      => 1,
                        'size'   => $size,
                    ];
                    if ($model === 'dall-e-3') {
                        $fallbackPayload['quality'] = 'standard';
                    }

                    $res = $this->http->post($this->base . '/images/generations', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->key,
                            'Content-Type'  => 'application/json',
                        ],
                        'json'    => $fallbackPayload,
                        'timeout' => 60,
                    ]);

                    $json = json_decode((string) $res->getBody(), true);

                    if (!empty($json['data'][0]['url'])) {
                        return $json['data'][0]['url'];
                    } elseif (!empty($json['data'][0]['b64_json'])) {
                        return 'data:image/png;base64,' . $json['data'][0]['b64_json'];
                    }
                } catch (\Throwable $e2) {
                    Log::warning("OpenAI Image Generation fallback for model {$model} failed: " . $e2->getMessage());
                }
                continue;
            }
        }

        Log::warning('OpenAI Image Generation failed across all models, attempting fallback engine', ['prompt' => $prompt]);

        // شبكة أمان بديلة في حال انتهاء رصيد OpenAI أو نقص صلاحيات المفتاح
        try {
            $fallbackUrl = 'https://image.pollinations.ai/prompt/' . urlencode($prompt) . '?model=flux&width=1024&height=1024&nologo=true';
            $res = \Illuminate\Support\Facades\Http::timeout(35)->get($fallbackUrl);
            if ($res->successful() && strlen($res->body()) > 5000) {
                return 'data:image/png;base64,' . base64_encode($res->body());
            }
        } catch (\Throwable $e) {
            Log::error('Fallback image generation failed: ' . $e->getMessage());
        }

        return null;
    }
}
