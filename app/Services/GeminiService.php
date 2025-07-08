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
        Log::info('Starting AI response generation', [
            'user_id' => $user->id,
            'prompt_length' => strlen($prompt),
            'api_configured' => $this->isConfigured(),
            'api_key_length' => $this->apiKey ? strlen($this->apiKey) : 0
        ]);

        if (!$this->isConfigured()) {
            Log::warning('AI service not configured', [
                'api_key' => $this->apiKey ? 'present' : 'missing'
            ]);
            return [
                'success' => false,
                'error' => 'AI service is not properly configured. Please check your API key.',
                'error_type' => 'configuration',
                'latency_ms' => 0
            ];
        }

        $startTime = microtime(true);
        $maxRetries = 3;
        $baseDelay = 1; // seconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Add attempt info to logs
                if ($attempt > 1) {
                    Log::info("AI generation attempt {$attempt}/{$maxRetries}");
                }
                
                // Format prompt for engineering context
                $engineeringPrompt = $this->formatEngineeringPrompt($prompt);
                
                $response = Http::timeout(30)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$this->baseUrl}/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", [
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
                    Log::info('Gemini API Response', ['data' => $data, 'attempt' => $attempt]);
                    
                    $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
                    
                    // Log the prompt and response
                    $this->promptLogRepository->create([
                        'user_id' => $user->id,
                        'prompt' => $prompt,
                        'response' => $generatedText,
                        'model_used' => 'gemini-1.5-flash',
                        'latency_ms' => $latencyMs
                    ]);

                    return [
                        'success' => true,
                        'response' => $generatedText,
                        'latency_ms' => $latencyMs,
                        'attempts' => $attempt
                    ];
                } else {
                    $statusCode = $response->status();
                    Log::warning("API request failed on attempt {$attempt}", [
                        'status' => $statusCode,
                        'body' => $response->body()
                    ]);
                    
                    // If it's a 503 (overloaded) error and we have attempts left, retry
                    if ($statusCode === 503 && $attempt < $maxRetries) {
                        $delay = $baseDelay * pow(2, $attempt - 1); // Exponential backoff
                        Log::info("Retrying in {$delay} seconds...");
                        sleep($delay);
                        continue;
                    }
                    
                    // If we've exhausted retries or it's a different error, return error
                    $errorMessage = $this->parseApiError($response);
                    Log::error('Gemini API Error', [
                        'status' => $response->status(),
                        'headers' => $response->headers(),
                        'body' => $response->body(),
                        'request_url' => "{$this->baseUrl}/models/gemini-1.5-flash:generateContent?key=***",
                        'attempts' => $attempt
                    ]);
                    
                    return [
                        'success' => false,
                        'error' => $errorMessage,
                        'error_type' => $this->getErrorType($response->status()),
                        'latency_ms' => $latencyMs,
                        'attempts' => $attempt
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("Connection error on attempt {$attempt}: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    $delay = $baseDelay * pow(2, $attempt - 1);
                    Log::info("Retrying connection in {$delay} seconds...");
                    sleep($delay);
                    continue;
                }
                
                $endTime = microtime(true);
                $latencyMs = round(($endTime - $startTime) * 1000);
                
                Log::error('Gemini Connection Error: ' . $e->getMessage());
                
                return [
                    'success' => false,
                    'error' => 'Unable to connect to AI service. Please check your internet connection.',
                    'error_type' => 'connection',
                    'latency_ms' => $latencyMs,
                    'attempts' => $attempt
                ];
            } catch (\Illuminate\Http\Client\RequestException $e) {
                Log::warning("Request error on attempt {$attempt}: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    $delay = $baseDelay * pow(2, $attempt - 1);
                    Log::info("Retrying request in {$delay} seconds...");
                    sleep($delay);
                    continue;
                }
                
                $endTime = microtime(true);
                $latencyMs = round(($endTime - $startTime) * 1000);
                
                Log::error('Gemini Request Error: ' . $e->getMessage());
                
                return [
                    'success' => false,
                    'error' => 'Request to AI service failed. Please try again later.',
                    'error_type' => 'request',
                    'latency_ms' => $latencyMs,
                    'attempts' => $attempt
                ];
            } catch (\Exception $e) {
                Log::warning("General error on attempt {$attempt}: " . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    $delay = $baseDelay * pow(2, $attempt - 1);
                    Log::info("Retrying after error in {$delay} seconds...");
                    sleep($delay);
                    continue;
                }
                
                $endTime = microtime(true);
                $latencyMs = round(($endTime - $startTime) * 1000);
                
                Log::error('Gemini Service Exception: ' . $e->getMessage());
                
                return [
                    'success' => false,
                    'error' => 'An unexpected error occurred while processing your request.',
                    'error_type' => 'unknown',
                    'latency_ms' => $latencyMs,
                    'attempts' => $attempt
                ];
            }
        }
        
        // This should never be reached, but just in case
        $endTime = microtime(true);
        $latencyMs = round(($endTime - $startTime) * 1000);
        
        return [
            'success' => false,
            'error' => 'All retry attempts failed.',
            'error_type' => 'exhausted',
            'latency_ms' => $latencyMs,
            'attempts' => $maxRetries
        ];
    }

    public function generatePostContent(string $idea, User $user): array
    {
        Log::info('Generating post content', [
            'idea' => $idea,
            'user_id' => $user->id,
            'api_configured' => $this->isConfigured()
        ]);

        $prompt = "Generate a professional social media post for engineers based on this idea: {$idea}. " .
                 "Make it engaging, informative, and suitable for a professional engineering social network. " .
                 "Keep it under 280 characters and include relevant engineering hashtags.";
        
        $result = $this->generateResponse($prompt, $user);
        
        // If AI generation fails, provide fallback content
        if (!$result['success']) {
            Log::warning('AI generation failed, using fallback', [
                'error' => $result['error'] ?? 'Unknown error',
                'error_type' => $result['error_type'] ?? 'unknown'
            ]);
            
            $fallbackContent = $this->generateFallbackContent($idea);
            
            // Log the fallback usage
            $this->promptLogRepository->create([
                'user_id' => $user->id,
                'prompt' => $prompt,
                'response' => $fallbackContent,
                'model_used' => 'fallback',
                'latency_ms' => 0
            ]);
            
            return [
                'success' => true,
                'response' => $fallbackContent,
                'latency_ms' => 0,
                'is_fallback' => true,
                'original_error' => $result['error'] ?? 'Unknown error'
            ];
        }
        
        Log::info('AI generation successful', [
            'response_length' => strlen($result['response']),
            'latency_ms' => $result['latency_ms']
        ]);
        
        return $result;
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
    
    private function generateFallbackContent(string $idea): string
    {
        $fallbackTemplates = [
            "🔧 Engineering Insight: {idea}\n\nSharing my thoughts on this important engineering topic. What's your experience with this? #Engineering #Innovation",
            "💡 Quick Engineering Thought: {idea}\n\nAlways learning something new in engineering! Would love to hear your perspectives. #EngineeringLife #TechTalk",
            "⚙️ Engineering Discussion: {idea}\n\nLet's discuss this engineering concept! Share your insights below. #Engineering #ProfessionalDevelopment",
            "🛠️ From the Engineering World: {idea}\n\nConstantly amazed by engineering innovations. What are your thoughts? #Engineering #Technology",
            "📐 Engineering Focus: {idea}\n\nExploring engineering concepts that shape our world. Join the conversation! #Engineering #Innovation"
        ];
        
        $template = $fallbackTemplates[array_rand($fallbackTemplates)];
        
        // Limit idea length and add ellipsis if needed
        $shortIdea = strlen($idea) > 150 ? substr($idea, 0, 147) . '...' : $idea;
        
        return str_replace('{idea}', $shortIdea, $template);
    }
    
    private function parseApiError($response): string
    {
        $statusCode = $response->status();
        $body = $response->json();
        
        switch ($statusCode) {
            case 400:
                return 'Invalid request to AI service. Please try rephrasing your idea.';
            case 401:
                return 'AI service authentication failed. Please contact support.';
            case 403:
                return 'AI service access denied. Your request may have been blocked.';
            case 429:
                return 'AI service is busy. Please wait a moment and try again.';
            case 500:
            case 502:
            case 503:
                return 'AI service is temporarily unavailable. Please try again later.';
            default:
                if (isset($body['error']['message'])) {
                    return 'AI service error: ' . $body['error']['message'];
                }
                return 'AI service returned an error. Please try again.';
        }
    }
    
    private function getErrorType(int $statusCode): string
    {
        if ($statusCode >= 400 && $statusCode < 500) {
            return 'client_error';
        } elseif ($statusCode >= 500) {
            return 'server_error';
        }
        return 'unknown';
    }
}
