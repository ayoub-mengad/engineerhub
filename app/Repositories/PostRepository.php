<?php

namespace App\Repositories;

use App\Contracts\PostRepositoryInterface;
use App\Contracts\FriendshipRepositoryInterface;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    private FriendshipRepositoryInterface $friendshipRepository;

    public function __construct(FriendshipRepositoryInterface $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    public function findById(int $id): ?Post
    {
        return Post::with('user')->find($id);
    }
    
    public function create(array $data): Post
    {
        return Post::create($data);
    }
    
    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }
    
    public function delete(Post $post): bool
    {
        return $post->delete();
    }
    
    public function getByUser(User $user): Collection
    {
        return Post::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function getPublicPosts(int $limit = 50): Collection
    {
        return Post::with('user')
            ->where('visibility', 'public')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getFeedForUser(User $user, int $limit = 50): Collection
    {
        // Get friends' IDs
        $friends = $this->friendshipRepository->getFriends($user);
        $friendIds = $friends->pluck('id')->toArray();
        
        return Post::with('user')
            ->where(function ($query) use ($user, $friendIds) {
                // User's own posts
                $query->where('user_id', $user->id)
                    // OR public posts
                    ->orWhere('visibility', 'public')
                    // OR friends' posts (both public and friends_only)
                    ->orWhere(function ($subQuery) use ($friendIds) {
                        $subQuery->whereIn('user_id', $friendIds);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getFriendsPostsForUser(User $user, int $limit = 50): Collection
    {
        $friends = $this->friendshipRepository->getFriends($user);
        $friendIds = $friends->pluck('id')->toArray();
        
        return Post::with('user')
            ->whereIn('user_id', $friendIds)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function canUserDelete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
