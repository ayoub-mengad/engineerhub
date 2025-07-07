<?php

namespace App\Services;

use App\Contracts\FriendshipServiceInterface;
use App\Contracts\FriendshipRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\User;

class FriendshipService implements FriendshipServiceInterface
{
    private FriendshipRepositoryInterface $friendshipRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        FriendshipRepositoryInterface $friendshipRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->friendshipRepository = $friendshipRepository;
        $this->userRepository = $userRepository;
    }

    public function sendFriendRequest(User $user, User $friend): array
    {
        if ($user->id === $friend->id) {
            return [
                'success' => false,
                'message' => 'You cannot send a friend request to yourself.'
            ];
        }

        // Check if friendship already exists
        if ($this->friendshipRepository->findFriendship($user, $friend)) {
            return [
                'success' => false,
                'message' => 'Friend request already exists or you are already friends.'
            ];
        }

        try {
            $this->friendshipRepository->create([
                'user_id' => $user->id,
                'friend_id' => $friend->id,
                'status' => 'pending'
            ]);

            return [
                'success' => true,
                'message' => 'Friend request sent successfully!'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send friend request.'
            ];
        }
    }

    public function acceptFriendRequest(int $friendshipId, User $user): array
    {
        $friendship = $this->friendshipRepository->findById($friendshipId);

        if (!$friendship) {
            return [
                'success' => false,
                'message' => 'Friend request not found.'
            ];
        }

        if ($friendship->friend_id !== $user->id) {
            return [
                'success' => false,
                'message' => 'You are not authorized to accept this friend request.'
            ];
        }

        if ($friendship->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'This friend request has already been processed.'
            ];
        }

        try {
            $this->friendshipRepository->update($friendship, ['status' => 'accepted']);

            return [
                'success' => true,
                'message' => 'Friend request accepted!'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to accept friend request.'
            ];
        }
    }

    public function declineFriendRequest(int $friendshipId, User $user): array
    {
        $friendship = $this->friendshipRepository->findById($friendshipId);

        if (!$friendship) {
            return [
                'success' => false,
                'message' => 'Friend request not found.'
            ];
        }

        if ($friendship->friend_id !== $user->id) {
            return [
                'success' => false,
                'message' => 'You are not authorized to decline this friend request.'
            ];
        }

        try {
            $this->friendshipRepository->delete($friendship);

            return [
                'success' => true,
                'message' => 'Friend request declined.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to decline friend request.'
            ];
        }
    }

    public function removeFriend(User $user, User $friend): array
    {
        if (!$this->friendshipRepository->areFriends($user, $friend)) {
            return [
                'success' => false,
                'message' => 'You are not friends with this user.'
            ];
        }

        try {
            $this->friendshipRepository->removeFriendship($user, $friend);

            return [
                'success' => true,
                'message' => 'Friend removed successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to remove friend.'
            ];
        }
    }

    public function removeFriendshipById(int $friendshipId, User $user): array
    {
        try {
            $friendship = $this->friendshipRepository->findById($friendshipId);
            
            if (!$friendship || ($friendship->user_id !== $user->id && $friendship->friend_id !== $user->id)) {
                return [
                    'success' => false,
                    'message' => 'Friendship not found or unauthorized.'
                ];
            }

            $this->friendshipRepository->deleteById($friendshipId);

            return [
                'success' => true,
                'message' => 'Friend removed successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to remove friend.'
            ];
        }
    }

    public function togglePostVisibility(int $friendshipId, User $user): array
    {
        try {
            $friendship = $this->friendshipRepository->findById($friendshipId);
            
            if (!$friendship || ($friendship->user_id !== $user->id && $friendship->friend_id !== $user->id)) {
                return [
                    'success' => false,
                    'message' => 'Friendship not found or unauthorized.'
                ];
            }

            $this->friendshipRepository->updateById($friendshipId, [
                'show_posts' => !$friendship->show_posts
            ]);

            return [
                'success' => true,
                'message' => 'Post visibility updated successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update post visibility.'
            ];
        }
    }

    public function cancelFriendRequest(int $friendshipId, User $user): array
    {
        try {
            $friendship = $this->friendshipRepository->findById($friendshipId);
            
            if (!$friendship || $friendship->user_id !== $user->id || $friendship->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Friend request not found or unauthorized.'
                ];
            }

            $this->friendshipRepository->deleteById($friendshipId);

            return [
                'success' => true,
                'message' => 'Friend request cancelled successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to cancel friend request.'
            ];
        }
    }

    public function getFriends(User $user): array
    {
        $friends = $this->friendshipRepository->getFriends($user);

        return [
            'success' => true,
            'friends' => $friends,
            'count' => $friends->count()
        ];
    }

    public function getPendingRequests(User $user): array
    {
        $pendingRequests = $this->friendshipRepository->getPendingRequests($user);

        return [
            'success' => true,
            'pending_requests' => $pendingRequests,
            'count' => $pendingRequests->count()
        ];
    }

    public function getSentRequests(User $user): array
    {
        $sentRequests = $this->friendshipRepository->getSentRequests($user);

        return [
            'success' => true,
            'sent_requests' => $sentRequests,
            'count' => $sentRequests->count()
        ];
    }

    public function searchUsers(string $query, User $currentUser): array
    {
        if (strlen(trim($query)) < 2) {
            return [
                'success' => false,
                'message' => 'Search query must be at least 2 characters long.',
                'users' => collect()
            ];
        }

        $users = $this->userRepository->search($query, 20)
            ->filter(function ($user) use ($currentUser) {
                return $user->id !== $currentUser->id;
            });

        // Add friendship status for each user
        $usersWithStatus = $users->map(function ($user) use ($currentUser) {
            $status = $this->friendshipRepository->getFriendshipStatus($currentUser, $user);
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'friendship_status' => $this->mapStatusToFrontend($status, $currentUser, $user),
                'is_friend' => $status === 'accepted'
            ];
        });

        return [
            'success' => true,
            'users' => $usersWithStatus,
            'count' => $usersWithStatus->count()
        ];
    }

    public function getAllAvailableUsers(User $currentUser): array
    {
        $users = $this->userRepository->getAllExcept($currentUser, 50);

        // Add friendship status for each user
        $usersWithStatus = $users->map(function ($user) use ($currentUser) {
            $status = $this->friendshipRepository->getFriendshipStatus($currentUser, $user);
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'friendship_status' => $this->mapStatusToFrontend($status, $currentUser, $user),
                'is_friend' => $status === 'accepted'
            ];
        });

        return [
            'success' => true,
            'users' => $usersWithStatus,
            'count' => $usersWithStatus->count()
        ];
    }

    private function mapStatusToFrontend(?string $status, User $currentUser, User $targetUser): string
    {
        if (!$status) {
            return 'none';
        }

        if ($status === 'accepted') {
            return 'friends';
        }

        if ($status === 'pending') {
            // Check if current user sent the request or received it
            $friendship = $this->friendshipRepository->findFriendship($currentUser, $targetUser);
            if ($friendship) {
                if ($friendship->user_id === $currentUser->id) {
                    return 'pending_sent';
                } else {
                    return 'pending_received';
                }
            }
        }

        return 'none';
    }
}
