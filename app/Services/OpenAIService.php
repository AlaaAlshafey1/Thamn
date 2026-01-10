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
        $this->http = new Client(['timeout' => 60]);

        // نفس الإعدادات اللي عندك
        $this->base = rtrim(
            config('services.openai.base', env('OPENAI_API_BASE', 'https://api.openai.com/v1/')),
            '/'
        );

        $this->key  = (string) env('OPENAI_API_KEY');
    }

    /**
     * Chat Completion (عام)
     */
public function chat(array $messages, string $model = 'gpt-4o-mini'): array
{
    $res = $this->http->post($this->base . '/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => 'application/json',
        ],
        'json' => [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.1,
            'response_format' => [
                'type' => 'json_object'
            ],
        ],
    ]);

    return json_decode((string) $res->getBody(), true);
}

    /**
     * ===============================
     * تقييم سلعة (AI Valuation)
     * ===============================
     */
    public function evaluateProduct(string $prompt): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'أنت خبير محترف في تثمين السلع وبالأخص السوق السعودي.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ],
        ];

        $response = $this->chat($messages, 'gpt-5-mini');

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
}
