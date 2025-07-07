<?php

namespace App\Providers;

use App\Contracts\AIServiceInterface;
use App\Contracts\FriendshipRepositoryInterface;
use App\Contracts\FriendshipServiceInterface;
use App\Contracts\PostRepositoryInterface;
use App\Contracts\PostServiceInterface;
use App\Contracts\PromptLogRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\FriendshipRepository;
use App\Repositories\PostRepository;
use App\Repositories\PromptLogRepository;
use App\Repositories\UserRepository;
use App\Services\FriendshipService;
use App\Services\GeminiService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(FriendshipRepositoryInterface::class, FriendshipRepository::class);
        $this->app->bind(PromptLogRepositoryInterface::class, PromptLogRepository::class);

        // Service bindings
        $this->app->bind(AIServiceInterface::class, GeminiService::class);
        $this->app->bind(FriendshipServiceInterface::class, FriendshipService::class);
        $this->app->bind(PostServiceInterface::class, PostService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
