<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class AiReviewService
{
    /**
     * AI server base URL
     */
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ai.base_url');
    }

    /**
     * Send code to AI server and get review response
     *
     * @throws \RuntimeException
     */
    public function review(string $code, string $language): array
    {
        try {
            $response = Http::timeout(120) // allow longer time for long responses
                ->acceptJson()
                ->asJson()
                ->post($this->baseUrl . '/ai/review', [
                    'code'     => $code,
                    'language' => $language,
                ]);

            // Check HTTP errors
            if ($response->failed()) {
                throw new \RuntimeException(
                    'AI server error (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();

            // Check if AI service returned "success": false
            if (isset($data['success']) && $data['success'] === false) {
                $message = $data['message'] ?? 'AI service unavailable';
                throw new \RuntimeException('AI server unavailable: ' . $message);
            }

            return $data;

        } catch (RequestException $e) {
            throw new \RuntimeException(
                'AI request failed: ' . $e->getMessage()
            );
        }
    }
}