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
