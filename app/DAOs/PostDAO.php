<?php

namespace App\DAOs;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PostDAO
{
    private $friendshipDAO;

    public function __construct(FriendshipDAO $friendshipDAO)
    {
        $this->friendshipDAO = $friendshipDAO;
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function getById(int $id): ?Post
    {
        return Post::with('user')->find($id);
    }

    public function getPostsByUser(User $user): Collection
    {
        return $user->posts()->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getFeedPosts(User $user): Collection
    {
        // Get user's friends using the DAO
        $friends = $this->friendshipDAO->getFriends($user);
        $friendIds = $friends->pluck('id')->toArray();
        
        return Post::with('user')
            ->where(function ($query) use ($user, $friendIds) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($subQuery) use ($friendIds) {
                        $subQuery->whereIn('user_id', $friendIds)
                            ->where('visibility', 'friends_only');
                    })
                    ->orWhere('visibility', 'public');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function canUserDeletePost(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
