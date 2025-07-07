<?php

namespace App\DAOs;

use App\Models\PromptLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PromptLogDAO
{
    public function create(array $data): PromptLog
    {
        return PromptLog::create($data);
    }

    public function getByUser(User $user): Collection
    {
        return $user->promptLogs()->orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): ?PromptLog
    {
        return PromptLog::find($id);
    }

    public function getRecentLogs(User $user, int $limit = 10): Collection
    {
        return $user->promptLogs()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getUserStats(User $user): array
    {
        $logs = $user->promptLogs();
        
        return [
            'total_prompts' => $logs->count(),
            'average_latency' => $logs->whereNotNull('latency_ms')->avg('latency_ms'),
            'most_used_model' => $logs->select('model_used')
                ->groupBy('model_used')
                ->orderByRaw('COUNT(*) DESC')
                ->first()?->model_used ?? 'N/A'
        ];
    }
}
