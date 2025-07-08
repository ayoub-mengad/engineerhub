<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# EngineerHub Project Instructions

This is a Laravel 12 social media platform specifically designed for engineers, featuring:

## Architecture Patterns
- **DAO Pattern**: All data access goes through Data Access Objects in `app/DAOs/`
- **Service Layer**: Business logic separated into services like `GeminiService`
- **MVC Pattern**: Standard Laravel controllers, models, and views

## Key Features
- **Authentication**: Laravel Breeze with Blade templates
- **AI Integration**: Gemini AI for prompt responses and post generation
- **Friend System**: Send/accept friend requests, manage connections
- **Posts**: Create posts with public/friends-only visibility
- **Responsive Design**: Tailwind CSS with engineer-themed blue/grey color scheme

## Code Guidelines
- Use DAO classes for all database operations
- Keep controllers thin - delegate to DAOs and services
- Follow Laravel naming conventions
- Use type hints and return types
- Handle errors gracefully with try-catch blocks
- Include proper validation in form requests
- Use Laravel flash messages for notifications (success, error, warning, info)
- Include error_suggestions array for detailed user guidance

## Database Schema
- `users`: Standard Laravel users table
- `posts`: User posts with visibility settings
- `friendships`: Friend relationships with status and sharing preferences
- `prompt_logs`: AI interaction history with performance metrics

## Frontend
- Blade templates with Tailwind CSS
- JavaScript for AJAX calls (search, AI generation)
- Mobile-responsive design
- Engineer-themed UI with professional color palette
