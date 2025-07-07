<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptLog extends Model
{
    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'model_used',
        'latency_ms'
    ];

    protected $casts = [
        'latency_ms' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
