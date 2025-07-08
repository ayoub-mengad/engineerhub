<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\PromptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard to home
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Main application routes
Route::middleware('auth')->group(function () {
    // Test notification route
    Route::get('/test-notifications', function () {
        session()->flash('success', 'This is a test success notification!');
        session()->flash('error', 'This is a test error notification with suggestions.');
        session()->flash('error_suggestions', [
            'This is the first suggestion',
            'This is the second suggestion',
            'This is a third helpful tip'
        ]);
        session()->flash('warning', 'This is a test warning notification.');
        session()->flash('info', 'This is a test info notification.');
        return redirect()->route('home');
    });
    
    // Test AI generation route
    Route::get('/test-ai', function () {
        $aiService = app(\App\Contracts\AIServiceInterface::class);
        $user = auth()->user();
        
        try {
            $result = $aiService->generatePostContent('test engineering project', $user);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });
    
    // Test AI simple route (no auth required)
    Route::get('/test-ai-simple', function () {
        try {
            $aiService = app(\App\Contracts\AIServiceInterface::class);
            
            // Test if service is configured
            $isConfigured = $aiService->isConfigured();
            
            if (!$isConfigured) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI service is not configured properly',
                    'fallback_test' => 'This would be fallback content for: Building a bridge project'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'AI service is configured and ready',
                'api_key_status' => 'configured'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });
    
    // Direct AI generation test (no auth required)
    Route::post('/test-generate', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate(['idea' => 'required|string']);
            
            $aiService = app(\App\Contracts\AIServiceInterface::class);
            $user = \App\Models\User::first(); // Use first user for testing
            
            $result = $aiService->generatePostContent($request->idea, $user);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
    
    // Home/Feed
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Posts
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/generate', [PostController::class, 'generatePost'])->name('posts.generate');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Friendships
    Route::get('/friends', [FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/friends/search', [FriendshipController::class, 'search'])->name('friends.search');
    Route::get('/friends/all', [FriendshipController::class, 'getAllUsers'])->name('friends.all');
    Route::post('/friends/send', [FriendshipController::class, 'sendRequest'])->name('friends.send');
    Route::post('/friends/accept/{friendship}', [FriendshipController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/decline/{friendship}', [FriendshipController::class, 'declineRequest'])->name('friends.decline');
    Route::post('/friends/toggle/{friendship}', [FriendshipController::class, 'toggleVisibility'])->name('friends.toggle');
    Route::delete('/friends/remove/{friendship}', [FriendshipController::class, 'removeFriend'])->name('friends.remove');
    Route::delete('/friends/cancel/{friendship}', [FriendshipController::class, 'cancelRequest'])->name('friends.cancel');
    
    // Prompts
    Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
    Route::post('/prompts/ask', [PromptController::class, 'ask'])->name('prompts.ask');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
