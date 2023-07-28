<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;


Route::get('/weather/get-by-city', [WeatherController::class, 'getByCityName']);
Route::get('/weather/get-average-by-city', [WeatherController::class, 'getAverageWeatherByCity']);
