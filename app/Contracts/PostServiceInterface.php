<?php

namespace App\Contracts;

use App\Models\Post;
use App\Models\User;

interface PostServiceInterface
{
    public function createPost(array $data, User $user): array;
    
    public function deletePost(int $postId, User $user): array;
    
    public function getFeedPosts(User $user, string $filter = 'all'): array;
    
    public function getUserPosts(User $user): array;
}
