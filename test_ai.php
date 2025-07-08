<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get a test user
$user = App\Models\User::first();
if (!$user) {
    echo "No users found in database.\n";
    exit(1);
}

// Get the AI service
$aiService = app(App\Contracts\AIServiceInterface::class);

echo "Testing AI service configuration...\n";
echo "API configured: " . ($aiService->isConfigured() ? 'Yes' : 'No') . "\n";

echo "\nTesting AI post generation...\n";
$result = $aiService->generatePostContent('bridge design', $user);

echo "Result:\n";
print_r($result);

echo "\nDone.\n";
