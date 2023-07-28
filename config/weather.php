<?php

return [
    'rapid' => [
        'api_key'  => env('RAPID_API_KEY'),
        'base_url' => 'https://weatherapi-com.p.rapidapi.com',
    ],

    'open_weather' => [
        'api_key'  => env('OPEN_WEATHER_API_KEY'),
        'base_url' => 'https://api.openweathermap.org',
    ],
];
