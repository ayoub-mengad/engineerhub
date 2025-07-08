@if(session('success') || session('error') || session('warning') || session('info'))
    <div id="notification-container" class="fixed top-20 right-4 z-50 w-96">
        
        @if(session('success'))
            <div class="notification-item bg-green-50 border border-green-200 rounded-lg p-4 mb-3 shadow-lg" data-type="success">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-green-800">Success</h3>
                        <div class="mt-1 text-sm text-green-700">{{ session('success') }}</div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeNotification(this)" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="notification-item bg-red-50 border border-red-200 rounded-lg p-4 mb-3 shadow-lg" data-type="error">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-1 text-sm text-red-700">{{ session('error') }}</div>
                        @if(session('error_suggestions'))
                            <div class="mt-2">
                                <p class="text-xs font-medium text-red-800">Suggestions:</p>
                                <ul class="list-disc list-inside mt-1 text-xs text-red-700">
                                    @foreach(session('error_suggestions') as $suggestion)
                                        <li>{{ $suggestion }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeNotification(this)" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="notification-item bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-3 shadow-lg" data-type="warning">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-yellow-800">Warning</h3>
                        <div class="mt-1 text-sm text-yellow-700">{{ session('warning') }}</div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeNotification(this)" class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="notification-item bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3 shadow-lg" data-type="info">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800">Information</h3>
                        <div class="mt-1 text-sm text-blue-700">{{ session('info') }}</div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeNotification(this)" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-50 focus:ring-blue-600">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function closeNotification(button) {
            const notification = button.closest('.notification-item');
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }

        // Auto-close notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(function(notification) {
                // Add initial animation
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.transition = 'all 0.3s ease-in-out';
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 100);

                // Auto-close after 7 seconds (longer for error messages)
                const type = notification.dataset.type;
                const delay = type === 'error' ? 10000 : type === 'warning' ? 8000 : 5000;
                
                setTimeout(() => {
                    if (notification.parentNode) {
                        closeNotification(notification.querySelector('button'));
                    }
                }, delay);
            });
        });

        // Function to show dynamic notifications via JavaScript (for AJAX calls)
        function showNotification(type, message, suggestions = []) {
            let container = document.getElementById('notification-container');
            
            // Create container if it doesn't exist
            if (!container) {
                container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed top-20 right-4 z-50 w-96';
                document.body.appendChild(container);
            }
            
            const id = `notification-${Date.now()}`;
            
            let icon = '';
            let colorClasses = {
                bg: '',
                border: '',
                title: '',
                text: '',
                button: '',
                hover: ''
            };
            
            switch(type) {
                case 'success':
                    icon = '<svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                    colorClasses = {
                        bg: 'bg-green-50',
                        border: 'border-green-200',
                        title: 'text-green-800',
                        text: 'text-green-700',
                        button: 'bg-green-50 text-green-500',
                        hover: 'hover:bg-green-100'
                    };
                    break;
                case 'error':
                    icon = '<svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>';
                    colorClasses = {
                        bg: 'bg-red-50',
                        border: 'border-red-200',
                        title: 'text-red-800',
                        text: 'text-red-700',
                        button: 'bg-red-50 text-red-500',
                        hover: 'hover:bg-red-100'
                    };
                    break;
                case 'warning':
                    icon = '<svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';
                    colorClasses = {
                        bg: 'bg-yellow-50',
                        border: 'border-yellow-200',
                        title: 'text-yellow-800',
                        text: 'text-yellow-700',
                        button: 'bg-yellow-50 text-yellow-500',
                        hover: 'hover:bg-yellow-100'
                    };
                    break;
                case 'info':
                    icon = '<svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>';
                    colorClasses = {
                        bg: 'bg-blue-50',
                        border: 'border-blue-200',
                        title: 'text-blue-800',
                        text: 'text-blue-700',
                        button: 'bg-blue-50 text-blue-500',
                        hover: 'hover:bg-blue-100'
                    };
                    break;
            }
            
            let suggestionsHtml = '';
            if (suggestions.length > 0) {
                suggestionsHtml = `<div class="mt-2">
                    <p class="text-xs font-medium ${colorClasses.title}">Suggestions:</p>
                    <ul class="list-disc list-inside mt-1 text-xs ${colorClasses.text}">
                        ${suggestions.map(suggestion => `<li>${suggestion}</li>`).join('')}
                    </ul>
                </div>`;
            }
            
            const notificationHtml = `
                <div class="notification-item ${colorClasses.bg} border ${colorClasses.border} rounded-lg p-4 mb-3 shadow-lg" data-type="${type}" id="${id}">
                    <div class="flex">
                        <div class="flex-shrink-0">${icon}</div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium ${colorClasses.title}">${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
                            <div class="mt-1 text-sm ${colorClasses.text}">${message}</div>
                            ${suggestionsHtml}
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="closeNotification(this)" class="inline-flex ${colorClasses.button} rounded-md p-1.5 ${colorClasses.hover} focus:outline-none focus:ring-2 focus:ring-offset-2">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', notificationHtml);
            
            // Animate in
            const notification = document.getElementById(id);
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.style.transition = 'all 0.3s ease-in-out';
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Auto-close
            const delay = type === 'error' ? 10000 : type === 'warning' ? 8000 : 5000;
            setTimeout(() => {
                if (notification.parentNode) {
                    closeNotification(notification.querySelector('button'));
                }
            }, delay);
        }
    </script>
@endif
