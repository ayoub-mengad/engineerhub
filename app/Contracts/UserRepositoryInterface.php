<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    
    public function findByEmail(string $email): ?User;
    
    public function create(array $data): User;
    
    public function update(User $user, array $data): bool;
    
    public function delete(User $user): bool;
    
    public function search(string $query, int $limit = 20): Collection;
    
    public function getAllExcept(User $user, int $limit = 100): Collection;
    
    public function exists(int $id): bool;
}
