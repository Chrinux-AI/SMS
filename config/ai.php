<?php
/**
 * Verdant AI Configuration
 * API keys and settings for AI integrations
 */

return [
    // Grok API (Primary - Cloud)
    'grok' => [
        'api_key' => getenv('GROK_API_KEY') ?: '',
        'model' => 'grok-beta',
        'max_tokens' => 1000,
        'temperature' => 0.7,
    ],

    // Ollama (Fallback - Local/Offline)
    'ollama' => [
        'url' => getenv('OLLAMA_URL') ?: 'http://localhost:11434',
        'model' => getenv('OLLAMA_MODEL') ?: 'llama3.2:3b',
        // Alternative models: qwen2.5:7b, mistral:7b
    ],

    // Feature flags
    'features' => [
        'lesson_planner' => true,
        'homework_solver' => true,
        'quiz_generator' => true,
        'progress_summary' => true,
        'voice_input' => true,
        'image_input' => false, // Requires Grok vision
    ],

    // NERDC curriculum settings
    'curriculum' => [
        'standard' => 'NERDC',
        'levels' => [
            'primary' => ['Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6'],
            'jss' => ['JSS 1', 'JSS 2', 'JSS 3'],
            'sss' => ['SSS 1', 'SSS 2', 'SSS 3'],
        ],
        'core_subjects' => [
            'English Language', 'Mathematics', 'Basic Science', 'Social Studies',
            'Civic Education', 'Computer Studies', 'Agricultural Science',
            'Religious Studies', 'Physical Education', 'French'
        ],
    ],

    // Rate limiting
    'rate_limit' => [
        'requests_per_minute' => 20,
        'requests_per_day' => 500,
    ],
];
