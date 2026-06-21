<?php

return [
    // OpenRouter API configuration
    'api_key'  => env('OPENROUTER_API_KEY', ''),
    'base_url' => 'https://openrouter.ai/api/v1/chat/completions',
    'model'    => env('OPENROUTER_MODEL', 'meta-llama/llama-3.3-70b-instruct:free'),
    'timeout'  => 60, // seconds

    // Prompt configuration
    'max_jobs'   => 20,   // max jobs to send per request
    'temperature' => 0.2,  // low = more consistent results
];
