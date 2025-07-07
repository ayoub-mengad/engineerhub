<?php

namespace App\Contracts;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FriendshipRepositoryInterface
{
    public function findById(int $id): ?Friendship;
    
    public function create(array $data): Friendship;
    
    public function update(Friendship $friendship, array $data): bool;
    
    public function updateById(int $id, array $data): bool;
    
    public function delete(Friendship $friendship): bool;
    
    public function deleteById(int $id): bool;
    
    public function getFriends(User $user): Collection;
    
    public function getPendingRequests(User $user): Collection;
    
    public function getSentRequests(User $user): Collection;
    
    public function findFriendship(User $user, User $friend): ?Friendship;
    
    public function areFriends(User $user, User $friend): bool;
    
    public function hasPendingRequest(User $user, User $friend): bool;
    
    public function getFriendshipStatus(User $user, User $friend): ?string;
    
    public function removeFriendship(User $user, User $friend): bool;
}
