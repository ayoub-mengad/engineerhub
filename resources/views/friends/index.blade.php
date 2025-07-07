<x-app-layout>
    <div class="px-4 py-6 lg:py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Title -->
            <div class="mb-6">
                <h1 class="text-2xl font-medium text-[#202124]">Friends & Connections</h1>
                <p class="text-gray-600 mt-1">Connect with fellow engineers and build your network</p>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Search -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">🔍 Find Engineers</h3>
                    <div class="space-y-4">
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                id="searchInput" 
                                placeholder="Search for engineers by name..." 
                                class="flex-1 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                            >
                            <button 
                                id="showAllBtn"
                                class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 text-sm transition-colors duration-150">
                                Show All
                            </button>
                        </div>
                        <div id="searchResults" class="space-y-2"></div>
                    </div>
                </div>

                <!-- Pending Friend Requests -->
                @if($pendingRequests->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">📬 Pending Requests ({{ $pendingRequests->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($pendingRequests as $request)
                            @if($request->user)
                            <div class="flex items-center justify-between p-3 bg-[#F3F6F9] rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($request->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#202124]">{{ $request->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $request->user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <form action="{{ route('friends.accept', $request->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-3 py-1 text-sm transition-colors duration-150">
                                            Accept
                                        </button>
                                    </form>
                                    <form action="{{ route('friends.decline', $request->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg px-3 py-1 text-sm transition-colors duration-150">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Current Friends -->
                @if($friends->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">👥 Your Connections ({{ $friends->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($friends as $friendship)
                            @php
                                $friend = $friendship->user_id === auth()->id() ? $friendship->friend : $friendship->user;
                            @endphp
                            @if($friend)
                            <div class="flex items-center justify-between p-3 bg-[#F3F6F9] rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($friend->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#202124]">{{ $friend->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $friend->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($friendship->show_posts)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                            Posts Visible
                                        </span>
                                    @else
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            Posts Hidden
                                        </span>
                                    @endif
                                    <form action="{{ route('friends.toggle', $friendship->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-[#0A66C2] hover:text-[#004182] font-medium transition-colors duration-150">
                                            {{ $friendship->show_posts ? 'Hide Posts' : 'Show Posts' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('friends.remove', $friendship->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to remove this friend?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors duration-150">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sent Requests -->
                @if($sentRequests->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">📤 Sent Requests ({{ $sentRequests->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($sentRequests as $request)
                            @if($request->friend)
                            <div class="flex items-center justify-between p-3 bg-[#F3F6F9] rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($request->friend->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#202124]">{{ $request->friend->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $request->friend->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                        Pending
                                    </span>
                                    <form action="{{ route('friends.cancel', $request->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600 font-medium transition-colors duration-150">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- No Friends Yet -->
                @if($friends->count() === 0 && $pendingRequests->count() === 0 && $sentRequests->count() === 0)
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <div class="text-6xl mb-4">👥</div>
                        <h3 class="text-xl font-medium text-[#202124] mb-2">Start Building Your Network</h3>
                        <p class="text-gray-600 mb-4">Connect with fellow engineers to share knowledge and collaborate on projects.</p>
                        <p class="text-sm text-gray-500">Use the search above to find and connect with other engineers.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const showAllBtn = document.getElementById('showAllBtn');

        function displayUsers(users) {
            searchResults.innerHTML = '';
            
            if (users.length === 0) {
                searchResults.innerHTML = '<p class="text-gray-500 text-sm py-2">No engineers found.</p>';
                return;
            }

            users.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.className = 'flex items-center justify-between p-3 bg-[#F3F6F9] rounded-lg';
                
                let actionButton = '';
                const status = user.friendship_status || user.status; // Handle both field names
                
                if (status === 'none' || !status) {
                    actionButton = `
                        <form action="{{ route('friends.send') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="friend_id" value="${user.id}">
                            <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-3 py-1 text-sm transition-colors duration-150">
                                Connect
                            </button>
                        </form>
                    `;
                } else if (status === 'pending_sent') {
                    actionButton = '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Pending</span>';
                } else if (status === 'pending_received') {
                    actionButton = '<span class="text-xs bg-blue-100 text-[#0A66C2] px-2 py-1 rounded-full">Awaiting Response</span>';
                } else if (status === 'accepted' || status === 'friends') {
                    actionButton = '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Connected</span>';
                }
                
                userDiv.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                            ${user.name.charAt(0)}
                        </div>
                        <div>
                            <p class="font-medium text-[#202124]">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.email}</p>
                        </div>
                    </div>
                    <div>
                        ${actionButton}
                    </div>
                `;
                
                searchResults.appendChild(userDiv);
            });
        }

        // Search functionality
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('friends.search') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayUsers(data.users);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<p class="text-red-500 text-sm py-2">Search failed. Please try again.</p>';
                    });
            }, 300);
        });

        // Show all users functionality
        showAllBtn.addEventListener('click', function() {
            this.textContent = 'Loading...';
            this.disabled = true;
            
            fetch('{{ route('friends.all') }}')
                .then(response => response.json())
                .then(data => {
                    displayUsers(data.users);
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = '<p class="text-red-500 text-sm py-2">Failed to load users. Please try again.</p>';
                })
                .finally(() => {
                    this.textContent = 'Show All';
                    this.disabled = false;
                });
        });
    </script>
</x-app-layout>
