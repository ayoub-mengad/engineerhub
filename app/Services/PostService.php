<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Contracts\PostRepositoryInterface;
use App\Models\User;

class PostService implements PostServiceInterface
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(array $data, User $user): array
    {
        try {
            $postData = [
                'user_id' => $user->id,
                'content' => $data['content'],
                'visibility' => $data['visibility'] ?? 'public'
            ];

            $post = $this->postRepository->create($postData);

            return [
                'success' => true,
                'message' => 'Post created successfully!',
                'post' => $post
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create post.'
            ];
        }
    }

    public function deletePost(int $postId, User $user): array
    {
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            return [
                'success' => false,
                'message' => 'Post not found.'
            ];
        }

        if (!$this->postRepository->canUserDelete($user, $post)) {
            return [
                'success' => false,
                'message' => 'You can only delete your own posts.'
            ];
        }

        try {
            $this->postRepository->delete($post);

            return [
                'success' => true,
                'message' => 'Post deleted successfully!'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete post.'
            ];
        }
    }

    public function getFeedPosts(User $user, string $filter = 'all'): array
    {
        try {
            switch ($filter) {
                case 'my_posts':
                    $posts = $this->postRepository->getByUser($user);
                    break;
                case 'friends_posts':
                    $posts = $this->postRepository->getFriendsPostsForUser($user);
                    break;
                default:
                    $posts = $this->postRepository->getFeedForUser($user);
                    break;
            }

            return [
                'success' => true,
                'posts' => $posts,
                'count' => $posts->count(),
                'filter' => $filter
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load posts.',
                'posts' => collect(),
                'count' => 0,
                'filter' => $filter
            ];
        }
    }

    public function getUserPosts(User $user): array
    {
        try {
            $posts = $this->postRepository->getByUser($user);

            return [
                'success' => true,
                'posts' => $posts,
                'count' => $posts->count()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user posts.',
                'posts' => collect(),
                'count' => 0
            ];
        }
    }
}
