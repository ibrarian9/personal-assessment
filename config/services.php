<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'router9' => [
        'api_key' => env('ROUTER9_API_KEY'),
        'base_url' => env('ROUTER9_BASE_URL', 'https://api.router9.com/v1'),
        'model' => env('ROUTER9_MODEL', 'ag/gemini-2.5-flash'),
        'available_models' => [
            'Rekomendasi Utama' => [
                'ag/gemini-2.5-flash' => 'Gemini 2.5 Flash',
            ],
            'Gemini 3.8 Series' => [
                'ag/gemini-3.8-flash' => 'Gemini 3.8 Flash',
                'ag/gemini-3.8-flash-high' => 'Gemini 3.8 Flash High',
                'ag/gemini-3.8-flash-medium' => 'Gemini 3.8 Flash Medium',
                'ag/gemini-3.8-flash-low' => 'Gemini 3.8 Flash Low',
            ],
            'Gemini 3.7 Series' => [
                'ag/gemini-3.7-flash-high' => 'Gemini 3.7 Flash High',
                'ag/gemini-3.7-flash-medium' => 'Gemini 3.7 Flash Medium',
                'ag/gemini-3.7-flash-low' => 'Gemini 3.7 Flash Low',
            ],
            'Gemini 3.6 Series' => [
                'ag/gemini-3.6-flash-high' => 'Gemini 3.6 Flash High',
                'ag/gemini-3.6-flash-medium' => 'Gemini 3.6 Flash Medium',
                'ag/gemini-3.6-flash-low' => 'Gemini 3.6 Flash Low',
            ],
            'Gemini 3.5 & Flash Agent' => [
                'ag/gemini-3.5-flash-high' => 'Gemini 3.5 Flash High',
                'ag/gemini-3.5-flash-low' => 'Gemini 3.5 Flash Low',
                'ag/gemini-3.5-flash-extra-low' => 'Gemini 3.5 Flash Extra Low',
                'ag/gemini-3-flash' => 'Gemini 3 Flash',
                'ag/gemini-3-flash-agent' => 'Gemini 3.5 Flash Agent High',
            ],
            'Gemini Pro' => [
                'ag/gemini-pro-agent' => 'Gemini 3.1 Pro Agent',
                'ag/gemini-3.1-pro-low' => 'Gemini 3.1 Pro Low',
            ],
            'Anthropic Claude' => [
                'ag/claude-sonnet-4-6' => 'Claude Sonnet 4.6',
                'ag/claude-opus-4-6-thinking' => 'Claude Opus 4.6 Thinking',
            ],
            'Open Source & GPT' => [
                'ag/gpt-oss-120b-medium' => 'GPT-OSS 120B Medium',
            ],
        ],
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'), // 'gemini', 'openai', 'router9', 'fallback'
    ],

];
