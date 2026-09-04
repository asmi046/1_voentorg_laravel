<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ollama AI
    |--------------------------------------------------------------------------
    |
    | Параметры подключения к Ollama API для получения данных о товарах.
    |
    */

    'base_url' => env('OLLAMA_BASE_URL', 'https://api.ollama.com'),

    'model' => env('OLLAMA_MODEL', 'llama3.1'),

    'timeout' => env('OLLAMA_TIMEOUT', 60),

    'api_key' => env('OLLAMA_API_KEY'),

];
