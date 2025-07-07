<?php

namespace App\Contracts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function findById(int $id): ?Post;
    
    public function create(array $data): Post;
    
    public function update(Post $post, array $data): bool;
    
    public function delete(Post $post): bool;
    
    public function getByUser(User $user): Collection;
    
    public function getPublicPosts(int $limit = 50): Collection;
    
    public function getFeedForUser(User $user, int $limit = 50): Collection;
    
    public function getFriendsPostsForUser(User $user, int $limit = 50): Collection;
    
    public function canUserDelete(User $user, Post $post): bool;
}
