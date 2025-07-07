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
            return response()->json([
                'success' => true,
                'content' => $result['response']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->postService->deletePost($id, auth()->user());

        if ($result['success']) {
            return redirect()->route('home')->with('success', $result['message']);
        }

        return redirect()->route('home')->with('error', $result['message']);
    }
}
