<?php

namespace App\Services\Weather;

use App\Services\Weather\API\OpenWeatherApi;
use App\Services\Weather\API\RapidWeatherApi;
use App\Services\Weather\API\WeatherApiServiceInterface;
use App\Services\Weather\DTO\WeatherResultDTO;
use App\Services\Weather\Exceptions\CityNotFound;
use App\Services\Weather\Exceptions\InvalidAuth;

class Weather
{
    /**
     * @var WeatherApiServiceInterface[]
     **/
    private array $weatherServices = [];

    public function __construct(RapidWeatherApi $rapidWeatherApi, OpenWeatherApi $openWeatherApi)
    {
        $this->weatherServices[] = $rapidWeatherApi;
        $this->weatherServices[] = $openWeatherApi;
    }

    public function getWeatherByCity(string $city): array
    {
        $weatherData = [];

        foreach ($this->weatherServices as $service) {
            $serviceName = $service->getServiceName();
            try {
                $weatherData[$serviceName] = $service->getWeatherByCity($city);
            } catch (CityNotFound) {
                $weatherData[$serviceName] = ['error' => 'City Not Found'];
            } catch (InvalidAuth) {
                $weatherData[$serviceName] = ['error' => 'Invalid Auth, Check Api token'];
            } catch (\Exception) {
                $weatherData[$serviceName] = ['error' => 'Something went wrong'];
            }
        }
        return $weatherData;
    }

    public function getAverageWeatherByCity(string $city): WeatherResultDTO | array
    {
        $weatherData = $this->getWeatherByCity($city);
        $resultWeather = new WeatherResultDTO();

        foreach ($weatherData as $value) {
            if (is_array($value) && array_key_exists('error', $value)) {
                return $weatherData;
            }
            $resultWeather->tempInCelsius += $value->tempInCelsius/count($weatherData);
            $resultWeather->windSpeed += $value->windSpeed/count($weatherData);
        }

        return $resultWeather;
    }
}
