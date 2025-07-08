<?php

namespace App\Http\Controllers;

use App\Contracts\AIServiceInterface;
use App\Contracts\PostServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private PostServiceInterface $postService;
    private AIServiceInterface $aiService;

    public function __construct(PostServiceInterface $postService, AIServiceInterface $aiService)
    {
        $this->postService = $postService;
        $this->aiService = $aiService;
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'visibility' => 'required|in:public,friends_only'
        ]);

        $result = $this->postService->createPost([
            'content' => $request->content,
            'visibility' => $request->visibility
        ], auth()->user());

        if ($result['success']) {
            return redirect()->route('home')->with('success', $result['message']);
        }

        return redirect()->route('home')->with('error', $result['message']);
    }

    public function generatePost(Request $request): JsonResponse
    {
        $request->validate([
            'idea' => 'required|string|max:500'
        ]);

        $result = $this->aiService->generatePostContent($request->idea, auth()->user());

        if ($result['success']) {
            $response = [
                'success' => true,
                'content' => $result['response'],
                'latency_ms' => $result['latency_ms'] ?? 0
            ];
            
            // Add fallback information if applicable
            if (isset($result['is_fallback']) && $result['is_fallback']) {
                $response['is_fallback'] = true;
                $response['fallback_message'] = 'AI service is temporarily unavailable. We generated content using our backup templates. You can edit it before posting.';
            }
            
            return response()->json($response);
        }

        // Return error with suggestions for AJAX handling
        return response()->json([
            'success' => false,
            'error' => $result['error'],
            'error_type' => $result['error_type'] ?? 'unknown',
            'suggestions' => $this->getErrorSuggestions($result['error_type'] ?? 'unknown')
        ], 422); // Use 422 for validation errors
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->postService->deletePost($id, auth()->user());

        if ($result['success']) {
            return redirect()->route('home')->with('success', $result['message']);
        }

        return redirect()->route('home')->with('error', $result['message']);
    }
    
    private function getErrorSuggestions(string $errorType): array
    {
        switch ($errorType) {
            case 'configuration':
                return [
                    'Contact support to configure AI service',
                    'Try again later when service is configured'
                ];
            case 'connection':
                return [
                    'Check your internet connection',
                    'Try again in a few moments',
                    'Use manual post creation instead'
                ];
            case 'client_error':
                return [
                    'Try rephrasing your idea',
                    'Make your idea more specific',
                    'Use shorter, clearer descriptions'
                ];
            case 'server_error':
                return [
                    'AI service is temporarily down',
                    'Try again in a few minutes',
                    'Create your post manually for now'
                ];
            default:
                return [
                    'Try refreshing the page',
                    'Rephrase your idea and try again',
                    'Create your post manually'
                ];
        }
    }
}
