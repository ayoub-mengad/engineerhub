<?php

namespace App\Helpers;

class NotificationHelper
{
    /**
     * Flash a success notification
     */
    public static function success(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Flash an error notification with optional suggestions
     */
    public static function error(string $message, array $suggestions = []): void
    {
        session()->flash('error', $message);
        if (!empty($suggestions)) {
            session()->flash('error_suggestions', $suggestions);
        }
    }

    /**
     * Flash a warning notification
     */
    public static function warning(string $message): void
    {
        session()->flash('warning', $message);
    }

    /**
     * Flash an info notification
     */
    public static function info(string $message): void
    {
        session()->flash('info', $message);
    }

    /**
     * Flash an AI service error with appropriate suggestions
     */
    public static function aiError(string $errorType, string $message): void
    {
        $suggestions = self::getAiErrorSuggestions($errorType);
        self::error($message, $suggestions);
    }

    /**
     * Get appropriate suggestions based on AI error type
     */
    private static function getAiErrorSuggestions(string $errorType): array
    {
        switch ($errorType) {
            case 'configuration':
                return [
                    'Contact support to configure AI service',
                    'Try again later when service is configured'
                ];
            case 'connection':
                return [
                    'Check your internet connection',
                    'Try again in a few moments',
                    'Use manual post creation instead'
                ];
            case 'client_error':
                return [
                    'Try rephrasing your idea',
                    'Make your idea more specific',
                    'Use shorter, clearer descriptions'
                ];
            case 'server_error':
                return [
                    'AI service is temporarily down',
                    'Try again in a few minutes',
                    'Create your post manually for now'
                ];
            default:
                return [
                    'Try refreshing the page',
                    'Rephrase your idea and try again',
                    'Create your post manually'
                ];
        }
    }
}
