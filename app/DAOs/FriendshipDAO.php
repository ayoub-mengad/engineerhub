<?php

namespace App\DAOs;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FriendshipDAO
{
    public function sendFriendRequest(User $user, User $friend): ?Friendship
    {
        // Check if friendship already exists
        if ($this->friendshipExists($user, $friend)) {
            return null;
        }

        return Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'status' => 'pending'
        ]);
    }

    public function acceptFriendRequest(Friendship $friendship): bool
    {
        return $friendship->update(['status' => 'accepted']);
    }

    public function declineFriendRequest(Friendship $friendship): bool
    {
        return $friendship->delete();
    }

    public function removeFriend(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->delete() > 0;
    }

    public function getFriends(User $user): Collection
    {
        // Get all accepted friendships where the user is either the sender or receiver
        $friendships = Friendship::where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('friend_id', $user->id);
            })
            ->get();

        // Extract friend IDs
        $friendIds = $friendships->map(function ($friendship) use ($user) {
            return $friendship->user_id === $user->id 
                ? $friendship->friend_id 
                : $friendship->user_id;
        })->unique();

        // Return the actual User models
        return User::whereIn('id', $friendIds)->get();
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

    public function searchUsers(string $search, User $currentUser): Collection
    {
        return User::where('name', 'LIKE', "%{$search}%")
            ->where('id', '!=', $currentUser->id)
            ->limit(20)
            ->get();
    }

    public function friendshipExists(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->exists();
    }

    public function areFriends(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->where('status', 'accepted')->exists();
    }

    public function getFriendshipStatus(User $user, User $friend): ?string
    {
        $friendship = Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)->where('friend_id', $user->id);
        })->first();

        return $friendship ? $friendship->status : null;
    }
}
