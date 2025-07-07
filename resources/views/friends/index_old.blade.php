<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Friends & Connections') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Search -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">🔍 Find Engineers</h3>
                        <div class="space-y-4">
                            <input 
                                type="text" 
                                id="searchInput" 
                                placeholder="Search for engineers by name..." 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <div id="searchResults" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Pending Friend Requests -->
                @if($pendingRequests->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">📬 Pending Requests ({{ $pendingRequests->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($pendingRequests as $request)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            {{ substr($request->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $request->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $request->user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form action="{{ route('friends.accept', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                ✓ Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('friends.decline', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                ✗ Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Friends List -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">👥 My Friends ({{ $friends->count() }})</h3>
                    
                    @if($friends->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($friends as $friend)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ substr($friend->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold">{{ $friend->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $friend->email }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('friends.remove', $friend->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to remove {{ $friend->name }} from your friends?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded text-sm">
                                            🗑️ Unfriend
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <p class="text-lg">No friends yet</p>
                            <p class="text-sm">Use the search above to find and connect with fellow engineers!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch('{{ route("friends.search") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ search: query })
                    });

                    const data = await response.json();
                    displaySearchResults(data.users);
                } catch (error) {
                    console.error('Search error:', error);
                }
            }, 300);
        });

        function displaySearchResults(users) {
            if (users.length === 0) {
                searchResults.innerHTML = '<p class="text-gray-500 text-sm">No users found</p>';
                return;
            }

            searchResults.innerHTML = users.map(user => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            ${user.name.charAt(0)}
                        </div>
                        <div>
                            <p class="font-medium">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.email}</p>
                        </div>
                    </div>
                    <div>
                        ${getActionButton(user)}
                    </div>
                </div>
            `).join('');
        }

        function getActionButton(user) {
            if (user.is_friend) {
                return '<span class="text-green-600 text-sm">✓ Friends</span>';
            } else if (user.friendship_status === 'pending') {
                return '<span class="text-yellow-600 text-sm">⏳ Pending</span>';
            } else {
                return `
                    <form action="/friends/request/${user.id}" method="POST" class="inline">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            + Add Friend
                        </button>
                    </form>
                `;
            }
        }
    </script>
</x-app-layout>
