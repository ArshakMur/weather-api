<?php

namespace App\Providers;

use App\Services\Weather\API\OpenWeatherApi;
use App\Services\Weather\API\RapidWeatherApi;
use App\Services\Weather\Weather;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $rapidWeatherApi = new RapidWeatherApi();
        $openWeatherApi = new OpenWeatherApi();

        $this->app->singleton(RapidWeatherApi::class, fn() => $rapidWeatherApi);
        $this->app->singleton(OpenWeatherApi::class, fn() => $openWeatherApi);

        $this->app->singleton(Weather::class, fn() => new Weather($rapidWeatherApi, $openWeatherApi));

        $this->app->register(L5SwaggerServiceProvider::class);
    }
}
