<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test direct API call to Gemini
$apiKey = env('GEMINI_API_KEY');
$baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

echo "Testing direct Gemini API call...\n";
echo "API Key length: " . strlen($apiKey) . "\n";

$prompt = "Write a short social media post about civil engineering innovations.";

$client = new \GuzzleHttp\Client();

try {
    $response = $client->post("{$baseUrl}/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ]
        ],
        'timeout' => 30
    ]);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response Body:\n";
    echo $response->getBody()->getContents() . "\n";
    
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Client Error: " . $e->getResponse()->getStatusCode() . "\n";
    echo "Error Body: " . $e->getResponse()->getBody()->getContents() . "\n";
} catch (\GuzzleHttp\Exception\ServerException $e) {
    echo "Server Error: " . $e->getResponse()->getStatusCode() . "\n";
    echo "Error Body: " . $e->getResponse()->getBody()->getContents() . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
