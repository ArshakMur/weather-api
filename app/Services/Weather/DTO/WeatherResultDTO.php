<?php

namespace App\Services\Weather\DTO;

class WeatherResultDTO
{
    public function __construct(
        public float $tempInCelsius = 0,
        public float $windSpeed = 0
    )
    {}
}
