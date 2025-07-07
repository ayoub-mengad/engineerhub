<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Prompts History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $stats['total_prompts'] }}</div>
                        <div class="text-sm text-gray-500">Total Prompts</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ number_format($stats['average_latency'] ?? 0, 0) }}ms</div>
                        <div class="text-sm text-gray-500">Average Response Time</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['most_used_model'] }}</div>
                        <div class="text-sm text-gray-500">Most Used Model</div>
                    </div>
                </div>
            </div>

            <!-- AI Chat Interface -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">🤖 Ask AI Assistant</h3>
                    <form id="promptForm" class="space-y-4">
                        @csrf
                        <div>
                            <textarea 
                                id="promptInput" 
                                placeholder="Ask me anything about engineering, projects, or technical questions..." 
                                rows="4" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required></textarea>
                        </div>
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                            Send Message 🚀
                        </button>
                    </form>
                    
                    <div id="responseArea" class="mt-6 hidden">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="font-medium mb-2">AI Response:</h4>
                            <div id="responseText" class="text-gray-700 whitespace-pre-wrap"></div>
                            <div id="responseStats" class="text-xs text-gray-500 mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prompts History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-6">📝 Recent Conversations</h3>
                    
                    @if($prompts->count() > 0)
                        <div class="space-y-6">
                            @foreach($prompts as $prompt)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <div class="mb-2">
                                        <span class="text-sm text-gray-500">
                                            {{ $prompt->created_at->diffForHumans() }} 
                                            • {{ $prompt->model_used }}
                                            @if($prompt->latency_ms)
                                                • {{ $prompt->latency_ms }}ms
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <!-- User Prompt -->
                                    <div class="mb-3">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 bg-blue-50 p-3 rounded-lg">
                                                <p class="whitespace-pre-wrap">{{ $prompt->prompt }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- AI Response -->
                                    <div class="ml-11">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                AI
                                            </div>
                                            <div class="flex-1 bg-green-50 p-3 rounded-lg">
                                                <p class="whitespace-pre-wrap">{{ $prompt->response }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-6xl mb-4">🤖</div>
                            <p class="text-lg">No conversations yet</p>
                            <p class="text-sm">Start a conversation with the AI assistant above!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('promptForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            const input = document.getElementById('promptInput');
            const prompt = input.value.trim();
            
            if (!prompt) return;
            
            btn.textContent = 'Thinking... 🤔';
            btn.disabled = true;
            
            try {
                const startTime = Date.now();
                const response = await fetch('{{ route("prompts.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ prompt })
                });
                
                const data = await response.json();
                const endTime = Date.now();
                
                if (data.success) {
                    document.getElementById('responseText').textContent = data.response;
                    document.getElementById('responseStats').textContent = 
                        `Response time: ${data.latency_ms}ms • Model: gemini-pro`;
                    document.getElementById('responseArea').classList.remove('hidden');
                    input.value = '';
                    
                    // Reload page after 2 seconds to show updated history
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate response'));
                }
            } catch (error) {
                alert('Network error: ' + error.message);
            } finally {
                btn.textContent = 'Send Message 🚀';
                btn.disabled = false;
            }
        });
    </script>
</x-app-layout>
