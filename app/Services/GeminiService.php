<?php

namespace App\Services;

use App\Contracts\AIServiceInterface;
use App\Contracts\PromptLogRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AIServiceInterface
{
    private PromptLogRepositoryInterface $promptLogRepository;
    private ?string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct(PromptLogRepositoryInterface $promptLogRepository)
    {
        $this->promptLogRepository = $promptLogRepository;
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateResponse(string $prompt, User $user): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'AI service is not properly configured. Please check your API key.',
                'latency_ms' => 0
            ];
        }

        $startTime = microtime(true);
        
        try {
            // Format prompt for engineering context
            $engineeringPrompt = $this->formatEngineeringPrompt($prompt);
            
            $response = Http::timeout(30)->post("{$this->baseUrl}/models/gemini-pro:generateContent", [
                'key' => $this->apiKey,
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $engineeringPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            $endTime = microtime(true);
            $latencyMs = round(($endTime - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
                
                // Log the prompt and response
                $this->promptLogRepository->create([
                    'user_id' => $user->id,
                    'prompt' => $prompt,
                    'response' => $generatedText,
                    'model_used' => 'gemini-pro',
                    'latency_ms' => $latencyMs
                ]);

                return [
                    'success' => true,
                    'response' => $generatedText,
                    'latency_ms' => $latencyMs
                ];
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Failed to generate response from AI service.',
                    'latency_ms' => $latencyMs
                ];
            }
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $latencyMs = round(($endTime - $startTime) * 1000);
            
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'An error occurred while processing your request.',
                'latency_ms' => $latencyMs
            ];
        }
    }

    public function generatePostContent(string $idea, User $user): array
    {
        $prompt = "Generate a professional social media post for engineers based on this idea: {$idea}. " .
                 "Make it engaging, informative, and suitable for a professional engineering social network. " .
                 "Keep it under 280 characters and include relevant engineering hashtags.";
        
        return $this->generateResponse($prompt, $user);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_gemini_api_key_here';
    }

    private function formatEngineeringPrompt(string $prompt): string
    {
        return "You are an AI assistant specializing in engineering topics, particularly civil engineering. " .
               "Please provide helpful, accurate, and professional responses to engineering-related questions. " .
               "If the question is not related to engineering, still provide a helpful response but try to relate it to engineering concepts when possible.\n\n" .
               "User question: {$prompt}";
    }
}
