<?php

namespace App\Repositories;

use App\Contracts\PromptLogRepositoryInterface;
use App\Models\PromptLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromptLogRepository implements PromptLogRepositoryInterface
{
    public function findById(int $id): ?PromptLog
    {
        return PromptLog::with('user')->find($id);
    }
    
    public function create(array $data): PromptLog
    {
        return PromptLog::create($data);
    }
    
    public function getByUser(User $user): LengthAwarePaginator
    {
        return PromptLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
    
    public function getRecentByUser(User $user, int $limit = 10): Collection
    {
        return PromptLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getUserStats(User $user): array
    {
        $logs = PromptLog::where('user_id', $user->id);
        
        $totalPrompts = $logs->count();
        $averageLatency = $logs->whereNotNull('latency_ms')->avg('latency_ms');
        
        $mostUsedModel = PromptLog::where('user_id', $user->id)
            ->select('model_used')
            ->groupBy('model_used')
            ->orderByRaw('COUNT(*) DESC')
            ->first()?->model_used ?? 'N/A';
            
        return [
            'total_prompts' => $totalPrompts,
            'average_latency' => $averageLatency ?? 0,
            'most_used_model' => $mostUsedModel
        ];
    }
    
    public function getTotalCount(): int
    {
        return PromptLog::count();
    }
    
    public function getAverageLatency(): float
    {
        return PromptLog::whereNotNull('latency_ms')->avg('latency_ms') ?? 0.0;
    }
}
