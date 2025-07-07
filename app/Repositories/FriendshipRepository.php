<?php

namespace App\Repositories;

use App\Contracts\FriendshipRepositoryInterface;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FriendshipRepository implements FriendshipRepositoryInterface
{
    public function findById(int $id): ?Friendship
    {
        return Friendship::with(['user', 'friend'])->find($id);
    }
    
    public function create(array $data): Friendship
    {
        return Friendship::create($data);
    }
    
    public function update(Friendship $friendship, array $data): bool
    {
        return $friendship->update($data);
    }
    
    public function updateById(int $id, array $data): bool
    {
        $friendship = $this->findById($id);
        if (!$friendship) {
            return false;
        }
        return $friendship->update($data);
    }
    
    public function delete(Friendship $friendship): bool
    {
        return $friendship->delete();
    }
    
    public function deleteById(int $id): bool
    {
        $friendship = $this->findById($id);
        if (!$friendship) {
            return false;
        }
        return $friendship->delete();
    }
    
    public function getFriends(User $user): Collection
    {
        // Get friendships where user initiated the friendship
        $friendsAsUser = Friendship::with('friend')
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->get();
            
        // Get friendships where user received the friendship
        $friendsAsFriend = Friendship::with('user')
            ->where('friend_id', $user->id)
            ->where('status', 'accepted')
            ->get();
            
        // Merge both collections
        return $friendsAsUser->merge($friendsAsFriend);
    }
    
    public function getPendingRequests(User $user): Collection
    {
        return Friendship::with('user')
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->get();
    }
    
    public function getSentRequests(User $user): Collection
    {
        return Friendship::with('friend')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();
    }
    
    public function findFriendship(User $user, User $friend): ?Friendship
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->first();
    }
    
    public function areFriends(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->where('status', 'accepted')->exists();
    }
    
    public function hasPendingRequest(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->where('status', 'pending')->exists();
    }
    
    public function getFriendshipStatus(User $user, User $friend): ?string
    {
        $friendship = $this->findFriendship($user, $friend);
        return $friendship ? $friendship->status : null;
    }
    
    public function removeFriendship(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->delete() > 0;
    }
}
