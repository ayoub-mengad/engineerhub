<x-app-layout>
    <!-- LinkedIn-style 3-column layout -->
    <div class="px-4 py-6 lg:py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Mobile: Single column -->
            <div class="lg:hidden space-y-4">
                <!-- Filter Buttons -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('home', ['filter' => 'all']) }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 {{ $filter === 'all' ? 'bg-[#0A66C2] text-white' : 'bg-[#EEF3F8] text-[#202124] hover:bg-blue-50' }}">
                            All Posts
                        </a>
                        <a href="{{ route('home', ['filter' => 'my_posts']) }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 {{ $filter === 'my_posts' ? 'bg-[#0A66C2] text-white' : 'bg-[#EEF3F8] text-[#202124] hover:bg-blue-50' }}">
                            My Posts
                        </a>
                        <a href="{{ route('home', ['filter' => 'friends_posts']) }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 {{ $filter === 'friends_posts' ? 'bg-[#0A66C2] text-white' : 'bg-[#EEF3F8] text-[#202124] hover:bg-blue-50' }}">
                            Friends' Posts
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- AI Post Generator -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">🤖 AI Post Generator</h3>
                    <form id="generatePostFormMobile" class="space-y-4">
                        @csrf
                        <div>
                            <textarea 
                                id="ideaInputMobile" 
                                placeholder="Describe your engineering project, idea, or topic..." 
                                rows="3" 
                                class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                required></textarea>
                        </div>
                        <button 
                            type="submit" 
                            id="generateBtnMobile"
                            class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                            Generate Post ✨
                        </button>
                    </form>
                    
                    <div id="generatedContentMobile" class="mt-4 hidden">
                        <div class="bg-[#F3F6F9] p-4 rounded-lg">
                            <h4 class="font-medium mb-2">Generated Content:</h4>
                            <div id="generatedTextMobile" class="text-gray-600 mb-4 text-[15px] leading-6"></div>
                            <form action="{{ route('posts.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <textarea 
                                    name="content" 
                                    id="postContentMobile" 
                                    rows="4" 
                                    class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                    placeholder="Edit your post content..."></textarea>
                                <div class="flex items-center space-x-4">
                                    <select name="visibility" class="border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2]">
                                        <option value="public">🌐 Public</option>
                                        <option value="friends_only">👥 Friends Only</option>
                                    </select>
                                    <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                                        Post 📝
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Regular Post Form -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-medium mb-4 text-[#202124]">✍️ Create Post</h3>
                    <form action="{{ route('posts.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea 
                                name="content" 
                                placeholder="What's on your engineering mind?" 
                                rows="4" 
                                class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                required></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <select name="visibility" class="border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2]">
                                <option value="public">🌐 Public</option>
                                <option value="friends_only">👥 Friends Only</option>
                            </select>
                            <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                                Post 📝
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Posts Feed -->
                <div class="space-y-4">
                    @forelse($posts as $post)
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($post->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-[#202124]">{{ $post->user->name }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $post->created_at->diffForHumans() }} 
                                            @if($post->visibility === 'friends_only')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-[#0A66C2]">
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
                                        <button type="submit" class="text-sm text-gray-500 hover:text-[#0A66C2] transition-colors duration-150">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="text-[15px] leading-6 text-[#202124]">
                                <p class="whitespace-pre-wrap">{{ $post->content }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                            <p class="text-gray-500">No posts to display.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Desktop: 3-column layout -->
            <div class="hidden lg:grid lg:grid-cols-12 lg:gap-6">
                <!-- Left Sidebar -->
                <div class="lg:col-span-3">
                    <div class="sticky top-20 space-y-6">
                        <!-- Profile Card -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-16 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold text-xl">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-medium text-[#202124]">{{ auth()->user()->name }}</h3>
                                    <p class="text-sm text-gray-500">Engineer</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Options -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <h3 class="font-medium text-[#202124] mb-3">Feed Options</h3>
                            <div class="space-y-2">
                                <a href="{{ route('home', ['filter' => 'all']) }}" 
                                   class="block px-3 py-2 rounded-lg text-sm transition-colors duration-150 {{ $filter === 'all' ? 'bg-[#0A66C2] text-white' : 'text-[#202124] hover:bg-blue-50' }}">
                                    All Posts
                                </a>
                                <a href="{{ route('home', ['filter' => 'my_posts']) }}" 
                                   class="block px-3 py-2 rounded-lg text-sm transition-colors duration-150 {{ $filter === 'my_posts' ? 'bg-[#0A66C2] text-white' : 'text-[#202124] hover:bg-blue-50' }}">
                                    My Posts
                                </a>
                                <a href="{{ route('home', ['filter' => 'friends_posts']) }}" 
                                   class="block px-3 py-2 rounded-lg text-sm transition-colors duration-150 {{ $filter === 'friends_posts' ? 'bg-[#0A66C2] text-white' : 'text-[#202124] hover:bg-blue-50' }}">
                                    Friends' Posts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Feed -->
                <div class="lg:col-span-6">
                    <div class="space-y-6">
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- AI Post Generator -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <h3 class="text-lg font-medium mb-4 text-[#202124]">🤖 AI Post Generator</h3>
                            <form id="generatePostForm" class="space-y-4">
                                @csrf
                                <div>
                                    <textarea 
                                        id="ideaInput" 
                                        placeholder="Describe your engineering project, idea, or topic..." 
                                        rows="3" 
                                        class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                        required></textarea>
                                </div>
                                <button 
                                    type="submit" 
                                    id="generateBtn"
                                    class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                                    Generate Post ✨
                                </button>
                            </form>
                            
                            <div id="generatedContent" class="mt-4 hidden">
                                <div class="bg-[#F3F6F9] p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">Generated Content:</h4>
                                    <div id="generatedText" class="text-gray-600 mb-4 text-[15px] leading-6"></div>
                                    <form action="{{ route('posts.store') }}" method="POST" class="space-y-3">
                                        @csrf
                                        <textarea 
                                            name="content" 
                                            id="postContent" 
                                            rows="4" 
                                            class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                            placeholder="Edit your post content..."></textarea>
                                        <div class="flex items-center space-x-4">
                                            <select name="visibility" class="border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2]">
                                                <option value="public">🌐 Public</option>
                                                <option value="friends_only">👥 Friends Only</option>
                                            </select>
                                            <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                                                Post 📝
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Regular Post Form -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <h3 class="text-lg font-medium mb-4 text-[#202124]">✍️ Create Post</h3>
                            <form action="{{ route('posts.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <textarea 
                                        name="content" 
                                        placeholder="What's on your engineering mind?" 
                                        rows="4" 
                                        class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                                        required></textarea>
                                </div>
                                <div class="flex items-center justify-between">
                                    <select name="visibility" class="border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2]">
                                        <option value="public">🌐 Public</option>
                                        <option value="friends_only">👥 Friends Only</option>
                                    </select>
                                    <button type="submit" class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                                        Post 📝
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Posts Feed -->
                        <div class="space-y-6">
                            @forelse($posts as $post)
                                <div class="bg-white rounded-lg shadow-sm p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-[#0A66C2] rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($post->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-[#202124]">{{ $post->user->name }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ $post->created_at->diffForHumans() }} 
                                                    @if($post->visibility === 'friends_only')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-[#0A66C2]">
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
                                                <button type="submit" class="text-sm text-gray-500 hover:text-[#0A66C2] transition-colors duration-150">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    <div class="text-[15px] leading-6 text-[#202124]">
                                        <p class="whitespace-pre-wrap">{{ $post->content }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                                    <p class="text-lg text-gray-500">No posts to display</p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        @if($filter === 'my_posts')
                                            You haven't created any posts yet.
                                        @elseif($filter === 'friends_posts')
                                            Your friends haven't shared any posts yet.
                                        @else
                                            Be the first to create a post!
                                        @endif
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-3">
                    <div class="sticky top-20 space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <h3 class="font-medium text-[#202124] mb-3">Your Network</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Posts</span>
                                    <span class="font-medium">{{ $posts->where('user_id', auth()->id())->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Friends</span>
                                    <span class="font-medium">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <h3 class="font-medium text-[#202124] mb-3">Recent Activity</h3>
                            <div class="text-sm text-gray-600">
                                <p>Welcome to EngineerHub! Start connecting with fellow engineers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fallback notification function if main one isn't loaded
        if (typeof showNotification === 'undefined') {
            function showNotification(type, message, suggestions = []) {
                console.log(`${type.toUpperCase()}: ${message}`, suggestions);
                
                // Create a simple notification div
                const notification = document.createElement('div');
                notification.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                    type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' :
                    type === 'error' ? 'bg-red-50 border border-red-200 text-red-800' :
                    type === 'warning' ? 'bg-yellow-50 border border-yellow-200 text-yellow-800' :
                    'bg-blue-50 border border-blue-200 text-blue-800'
                }`;
                
                notification.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                            <div class="text-sm mt-1">${message}</div>
                            ${suggestions.length ? `<ul class="text-xs mt-2 list-disc list-inside">${suggestions.map(s => `<li>${s}</li>`).join('')}</ul>` : ''}
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-lg">&times;</button>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing AI generation...');
            
            // Initialize both desktop and mobile forms
            initializeAIForm('generatePostForm', 'generateBtn', 'ideaInput', 'generatedText', 'postContent', 'generatedContent');
            initializeAIForm('generatePostFormMobile', 'generateBtnMobile', 'ideaInputMobile', 'generatedTextMobile', 'postContentMobile', 'generatedContentMobile');
        });
        
        function initializeAIForm(formId, btnId, inputId, textId, contentId, containerId) {
            const form = document.getElementById(formId);
            const btn = document.getElementById(btnId);
            const ideaInput = document.getElementById(inputId);
            
            console.log(`Initializing ${formId}:`, {
                form: !!form,
                btn: !!btn,
                ideaInput: !!ideaInput
            });
            
            if (!form || !btn || !ideaInput) {
                console.error(`Required elements not found for ${formId}:`, {form, btn, ideaInput});
                return;
            }

            form.addEventListener('submit', async (e) => {
                console.log(`Form ${formId} submitted!`);
                e.preventDefault();
                e.stopPropagation();
                
                const idea = ideaInput.value;
                console.log('Idea entered:', idea);
                
                if (!idea.trim()) {
                    console.log('Empty idea, showing error');
                    showNotification('error', 'Please enter an idea or topic for your post.', ['Describe your engineering project or idea', 'Be specific about what you want to share']);
                    return;
                }
                
                // Double check we prevented the default
                console.log('Event prevented, continuing with AJAX...');
                
                btn.textContent = 'Generating... ⏳';
                btn.disabled = true;
                
                console.log('Starting API request...');
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    console.log('CSRF token found:', !!csrfToken);
                    
                    const requestData = { idea: idea };
                    console.log('Request data:', requestData);
                    
                    const response = await fetch('{{ route("posts.generate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        const generatedText = document.getElementById(textId);
                        const postContent = document.getElementById(contentId);
                        const generatedContent = document.getElementById(containerId);
                        
                        console.log('Elements for display:', {
                            generatedText: !!generatedText,
                            postContent: !!postContent,
                            generatedContent: !!generatedContent
                        });
                        
                        if (generatedText && postContent && generatedContent) {
                            generatedText.textContent = data.content;
                            postContent.value = data.content;
                            generatedContent.classList.remove('hidden');
                            console.log('Content displayed successfully');
                        }
                        
                        // Show success notification
                        showNotification('success', 'Post content generated successfully! You can edit it before posting.');
                        
                        // Show fallback message if applicable
                        if (data.is_fallback) {
                            showNotification('warning', data.fallback_message || 'Content generated using backup templates due to AI service unavailability.');
                        }
                    } else {
                        console.log('Request failed:', data);
                        // Show error notification with suggestions
                        showNotification('error', data.error || 'Failed to generate post content.', data.suggestions || []);
                    }
                } catch (error) {
                    console.error('AI Generation Error:', error);
                    // Network or other errors
                    showNotification('error', 'Unable to connect to the server. Please check your internet connection and try again.', [
                        'Check your internet connection',
                        'Refresh the page and try again',
                        'Try creating your post manually'
                    ]);
                } finally {
                    console.log('Resetting button...');
                    btn.textContent = 'Generate Post ✨';
                    btn.disabled = false;
                }
            });
            
            console.log(`AI Generation form ${formId} initialized successfully!`);
        }
    </script>
</x-app-layout>
