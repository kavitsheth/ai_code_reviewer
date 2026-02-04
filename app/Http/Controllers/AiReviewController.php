<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiReviewService;

class AiReviewController extends Controller
{
    protected AiReviewService $aiService;

    public function __construct(AiReviewService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function review(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'language' => 'required|string',
        ]);

        try {
            $result = $this->aiService->review(
                $request->input('code'),
                $request->input('language')
            );

            return response()->json([
                'review' => $result['response'] ?? $result
            ]);

        } catch (\RuntimeException $e) {
            return response()->json([
                'review' => '❌ ' . $e->getMessage()
            ]);
        }
    }
}