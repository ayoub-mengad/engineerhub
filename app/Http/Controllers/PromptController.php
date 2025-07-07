<?php

namespace App\Http\Controllers;

use App\Contracts\AIServiceInterface;
use App\Contracts\PromptLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptController extends Controller
{
    private PromptLogRepositoryInterface $promptLogRepository;
    private AIServiceInterface $aiService;

    public function __construct(
        PromptLogRepositoryInterface $promptLogRepository,
        AIServiceInterface $aiService
    ) {
        $this->promptLogRepository = $promptLogRepository;
        $this->aiService = $aiService;
    }

    public function index(): View
    {
        $user = auth()->user();
        $prompts = $this->promptLogRepository->getByUser($user);
        $stats = $this->promptLogRepository->getUserStats($user);

        return view('prompts.index', compact('prompts', 'stats'));
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:2000'
        ]);

        $result = $this->aiService->generateResponse($request->prompt, auth()->user());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'latency_ms' => $result['latency_ms']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 400);
    }

    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:2000'
        ]);

        $result = $this->aiService->generateResponse($request->prompt, auth()->user());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'model' => $result['model'] ?? 'Gemini',
                'latency_ms' => $result['latency_ms'] ?? 0
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 400);
    }
}
