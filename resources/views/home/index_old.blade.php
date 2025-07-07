<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('EngineerHub Feed') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('home', ['filter' => 'all']) }}" 
                   class="px-3 py-1 rounded-md text-sm {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    All Posts
                </a>
                <a href="{{ route('home', ['filter' => 'my_posts']) }}" 
                   class="px-3 py-1 rounded-md text-sm {{ $filter === 'my_posts' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    My Posts
                </a>
                <a href="{{ route('home', ['filter' => 'friends_posts']) }}" 
                   class="px-3 py-1 rounded-md text-sm {{ $filter === 'friends_posts' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Friends' Posts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <!-- AI Post Generator -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🤖 AI Post Generator</h3>
                    <form id="generatePostForm" class="space-y-4">
                        @csrf
                        <div>
                            <textarea 
                                id="ideaInput" 
                                placeholder="Describe your engineering project, idea, or topic..." 
                                rows="3" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required></textarea>
                        </div>
                        <button 
                            type="submit" 
                            id="generateBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Generate Post ✨
                        </button>
                    </form>
                    
                    <div id="generatedContent" class="mt-4 hidden">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="font-medium mb-2">Generated Content:</h4>
                            <div id="generatedText" class="text-gray-700 mb-4"></div>
                            <form action="{{ route('posts.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <textarea 
                                    name="content" 
                                    id="postContent" 
                                    rows="4" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Edit your post content..."></textarea>
                                <div class="flex items-center space-x-4">
                                    <select name="visibility" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="public">🌐 Public</option>
                                        <option value="friends_only">👥 Friends Only</option>
                                    </select>
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                        Post 📝
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regular Post Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">✍️ Create Post</h3>
                    <form action="{{ route('posts.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea 
                                name="content" 
                                placeholder="What's on your engineering mind?" 
                                rows="4" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <select name="visibility" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="public">🌐 Public</option>
                                <option value="friends_only">👥 Friends Only</option>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Post 📝
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts Feed -->
            <div class="space-y-6">
                @forelse($posts as $post)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($post->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">{{ $post->user->name }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $post->created_at->diffForHumans() }} 
                                            @if($post->visibility === 'friends_only')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    👥 Friends Only
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($post->user_id === auth()->id())
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="prose max-w-none">
                                <p class="whitespace-pre-wrap">{{ $post->content }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            <p class="text-lg">No posts to display</p>
                            <p class="text-sm">
                                @if($filter === 'my_posts')
                                    You haven't created any posts yet.
                                @elseif($filter === 'friends_posts')
                                    Your friends haven't shared any posts yet.
                                @else
                                    Be the first to create a post!
                                @endif
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.getElementById('generatePostForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('generateBtn');
            const idea = document.getElementById('ideaInput').value;
            
            if (!idea.trim()) return;
            
            btn.textContent = 'Generating... ⏳';
            btn.disabled = true;
            
            try {
                const response = await fetch('{{ route("posts.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ idea })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('generatedText').textContent = data.content;
                    document.getElementById('postContent').value = data.content;
                    document.getElementById('generatedContent').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate content'));
                }
            } catch (error) {
                alert('Network error: ' + error.message);
            } finally {
                btn.textContent = 'Generate Post ✨';
                btn.disabled = false;
            }
        });
    </script>
</x-app-layout>
