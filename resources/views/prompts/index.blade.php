<x-app-layout>
    <div class="px-4 py-6 lg:py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Title -->
            <div class="mb-6">
                <h1 class="text-2xl font-medium text-[#202124]">AI Prompts History</h1>
                <p class="text-gray-600 mt-1">Your AI interactions and response analytics</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-center">
                        <div class="text-3xl font-semibold text-[#0A66C2] mb-1">{{ $stats['total_prompts'] }}</div>
                        <div class="text-sm text-gray-600">Total Prompts</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-center">
                        <div class="text-3xl font-semibold text-green-600 mb-1">{{ number_format($stats['average_latency'] ?? 0, 0) }}ms</div>
                        <div class="text-sm text-gray-600">Average Response Time</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-purple-600 mb-1">{{ $stats['most_used_model'] }}</div>
                        <div class="text-sm text-gray-600">Most Used Model</div>
                    </div>
                </div>
            </div>

            <!-- AI Chat Interface -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <h3 class="text-lg font-medium mb-4 text-[#202124]">🤖 Ask AI Assistant</h3>
                <form id="promptForm" class="space-y-4">
                    @csrf
                    <div>
                        <textarea 
                            id="promptInput" 
                            placeholder="Ask me anything about engineering, projects, or technical questions..." 
                            rows="4" 
                            class="w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                            required></textarea>
                    </div>
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-6 py-2 transition-colors duration-150">
                        Send Message 🚀
                    </button>
                </form>
                
                <!-- Response Area -->
                <div id="responseArea" class="mt-6 hidden">
                    <div class="bg-[#F3F6F9] p-4 rounded-lg">
                        <h4 class="font-medium mb-2 text-[#202124]">AI Response:</h4>
                        <div id="responseText" class="text-[15px] leading-6 text-gray-700 whitespace-pre-wrap"></div>
                        <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center text-sm text-gray-500">
                            <span id="responseTime"></span>
                            <span id="responseModel"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Prompts -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-lg font-medium mb-4 text-[#202124]">📝 Recent Interactions</h3>
                
                @if($prompts->count() > 0)
                    <div class="space-y-4">
                        @foreach($prompts as $prompt)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 transition-colors duration-150">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-[#202124]">Prompt</span>
                                        <span class="text-xs bg-[#EEF3F8] text-gray-600 px-2 py-1 rounded-full">
                                            {{ $prompt->model }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $prompt->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="text-sm text-gray-600 mb-1">Question:</div>
                                    <div class="text-[15px] leading-6 text-[#202124] bg-gray-50 p-3 rounded">
                                        {{ Str::limit($prompt->prompt, 200) }}
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="text-sm text-gray-600 mb-1">Response:</div>
                                    <div class="text-[15px] leading-6 text-gray-700 bg-[#F3F6F9] p-3 rounded">
                                        {{ Str::limit($prompt->response, 300) }}
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <div class="flex space-x-4">
                                        <span>Model: {{ $prompt->model }}</span>
                                        <span>Tokens: {{ $prompt->tokens_used ?? 'N/A' }}</span>
                                        <span>Response Time: {{ $prompt->response_time_ms ?? 'N/A' }}ms</span>
                                    </div>
                                    @if($prompt->success)
                                        <span class="text-green-600 font-medium">✓ Success</span>
                                    @else
                                        <span class="text-red-600 font-medium">✗ Failed</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $prompts->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">🤖</div>
                        <h3 class="text-xl font-medium text-[#202124] mb-2">No AI Interactions Yet</h3>
                        <p class="text-gray-600">Start a conversation with the AI assistant above to see your history here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('promptForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            const promptInput = document.getElementById('promptInput');
            const responseArea = document.getElementById('responseArea');
            const responseText = document.getElementById('responseText');
            const responseTime = document.getElementById('responseTime');
            const responseModel = document.getElementById('responseModel');
            
            const prompt = promptInput.value.trim();
            if (!prompt) return;
            
            // Show loading state
            btn.textContent = 'Thinking... 🤔';
            btn.disabled = true;
            responseArea.classList.add('hidden');
            
            const startTime = Date.now();
            
            try {
                const response = await fetch('{{ route("prompts.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ prompt })
                });
                
                const data = await response.json();
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                if (data.success) {
                    responseText.textContent = data.response;
                    responseTime.textContent = `Response time: ${duration}ms`;
                    responseModel.textContent = `Model: ${data.model || 'Gemini'}`;
                    responseArea.classList.remove('hidden');
                    
                    // Clear the input
                    promptInput.value = '';
                    
                    // Refresh the page after a delay to show the new prompt in history
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + (data.error || 'Failed to get AI response'));
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
