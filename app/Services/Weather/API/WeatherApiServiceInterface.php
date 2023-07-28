<?php

namespace App\Services\Weather\API;

use App\Services\Weather\DTO\WeatherResultDTO;
use App\Services\Weather\Exceptions\CityNotFound;
use App\Services\Weather\Exceptions\InvalidAuth;
use App\Services\Weather\Exceptions\UnexpectedResponse;
use App\Services\Weather\Exceptions\UnexpectedStatusCode;

interface WeatherApiServiceInterface
{
    /**
     * @throws InvalidAuth
     * @throws CityNotFound
     * @throws UnexpectedStatusCode
     * @throws UnexpectedResponse
     * @throws \JsonException
     */
    public function getWeatherByCity(string $city): WeatherResultDTO;

    public function getServiceName(): string;
}
