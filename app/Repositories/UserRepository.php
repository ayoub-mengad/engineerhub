<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }
    
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    
    public function create(array $data): User
    {
        return User::create($data);
    }
    
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
    
    public function delete(User $user): bool
    {
        return $user->delete();
    }
    
    public function search(string $query, int $limit = 20): Collection
    {
        return User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get();
    }
    
    public function getAllExcept(User $user, int $limit = 100): Collection
    {
        return User::where('id', '!=', $user->id)
            ->limit($limit)
            ->get();
    }
    
    public function exists(int $id): bool
    {
        return User::where('id', $id)->exists();
    }
}
