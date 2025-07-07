<?php

namespace App\Http\Controllers;

use App\Contracts\FriendshipServiceInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendshipController extends Controller
{
    private FriendshipServiceInterface $friendshipService;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        FriendshipServiceInterface $friendshipService,
        UserRepositoryInterface $userRepository
    ) {
        $this->friendshipService = $friendshipService;
        $this->userRepository = $userRepository;
    }

    public function index(): View
    {
        $user = auth()->user();
        $friendsResult = $this->friendshipService->getFriends($user);
        $pendingResult = $this->friendshipService->getPendingRequests($user);
        $sentResult = $this->friendshipService->getSentRequests($user);

        return view('friends.index', [
            'friends' => $friendsResult['friends'],
            'pendingRequests' => $pendingResult['pending_requests'],
            'sentRequests' => $sentResult['sent_requests']
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $result = $this->friendshipService->searchUsers($request->query, auth()->user());

        return response()->json([
            'users' => $result['users']
        ]);
    }

    public function getAllUsers(): JsonResponse
    {
        $result = $this->friendshipService->getAllAvailableUsers(auth()->user());

        return response()->json([
            'users' => $result['users']
        ]);
    }

    public function sendRequest(Request $request): RedirectResponse
    {
        $request->validate([
            'friend_id' => 'required|integer|exists:users,id'
        ]);

        $friend = $this->userRepository->findById($request->friend_id);
        
        if (!$friend) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $result = $this->friendshipService->sendFriendRequest(auth()->user(), $friend);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function acceptRequest(int $friendshipId): RedirectResponse
    {
        $result = $this->friendshipService->acceptFriendRequest($friendshipId, auth()->user());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function declineRequest(int $friendshipId): RedirectResponse
    {
        $result = $this->friendshipService->declineFriendRequest($friendshipId, auth()->user());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function removeFriend(int $userId): RedirectResponse
    {
        $friend = $this->userRepository->findById($userId);
        
        if (!$friend) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $result = $this->friendshipService->removeFriend(auth()->user(), $friend);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function removeFriendshipById(int $friendshipId): RedirectResponse
    {
        $result = $this->friendshipService->removeFriendshipById($friendshipId, auth()->user());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function toggleVisibility(int $friendshipId): RedirectResponse
    {
        $result = $this->friendshipService->togglePostVisibility($friendshipId, auth()->user());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function cancelRequest(int $friendshipId): RedirectResponse
    {
        $result = $this->friendshipService->cancelFriendRequest($friendshipId, auth()->user());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
