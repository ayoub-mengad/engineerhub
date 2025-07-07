<?php

namespace App\Contracts;

use App\Models\User;

interface FriendshipServiceInterface
{
    public function sendFriendRequest(User $user, User $friend): array;
    
    public function acceptFriendRequest(int $friendshipId, User $user): array;
    
    public function declineFriendRequest(int $friendshipId, User $user): array;
    
    public function removeFriend(User $user, User $friend): array;
    
    public function removeFriendshipById(int $friendshipId, User $user): array;
    
    public function togglePostVisibility(int $friendshipId, User $user): array;
    
    public function cancelFriendRequest(int $friendshipId, User $user): array;
    
    public function getFriends(User $user): array;
    
    public function getPendingRequests(User $user): array;
    
    public function getSentRequests(User $user): array;
    
    public function searchUsers(string $query, User $currentUser): array;
    
    public function getAllAvailableUsers(User $currentUser): array;
}
