<?php

namespace App\Contracts;

use App\Models\PromptLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PromptLogRepositoryInterface
{
    public function findById(int $id): ?PromptLog;
    
    public function create(array $data): PromptLog;
    
    public function getByUser(User $user): LengthAwarePaginator;
    
    public function getRecentByUser(User $user, int $limit = 10): Collection;
    
    public function getUserStats(User $user): array;
    
    public function getTotalCount(): int;
    
    public function getAverageLatency(): float;
}
