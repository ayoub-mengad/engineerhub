<?php

namespace App\Contracts;

use App\Models\User;

interface AIServiceInterface
{
    public function generateResponse(string $prompt, User $user): array;
    
    public function generatePostContent(string $idea, User $user): array;
    
    public function isConfigured(): bool;
}
