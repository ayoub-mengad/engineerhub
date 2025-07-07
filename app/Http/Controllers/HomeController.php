<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all'); // all, my_posts, friends_posts
        $user = auth()->user();

        $result = $this->postService->getFeedPosts($user, $filter);

        return view('home.index', [
            'posts' => $result['posts'],
            'filter' => $filter
        ]);
    }
}
