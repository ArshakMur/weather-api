<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\WeatherController;
use App\Services\Weather\Weather;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Mockery;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{

    public function testGetByCityNameWithValidCity(): void
    {
        $request = new FormRequest();
        $request->query->add([
            'city' => 'london',
        ]);

        $weatherServiceMock = Mockery::mock(Weather::class)
            ->shouldReceive('getWeatherByCity')
            ->once()
            ->andReturn([
                'Rapid Weather' => [
                    'tempInCelsius' => 25.5,
                    'windSpeed' => 10.2,
                ],
                'Open Weather' => [
                    'tempInCelsius' => 28.3,
                    'windSpeed' => 15.1,
                ],
            ])->getMock();

        $weatherController = new WeatherController($weatherServiceMock);
        $response = $weatherController->getByCityName($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(ResponseAlias::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('tempInCelsius', $responseData['Rapid Weather']);
        $this->assertArrayHasKey('windSpeed', $responseData['Rapid Weather']);
        $this->assertArrayHasKey('tempInCelsius', $responseData['Open Weather']);
        $this->assertArrayHasKey('windSpeed', $responseData['Open Weather']);
    }

    public function testGetAverageWeatherByCity(): void
    {
        $request = new FormRequest();
        $request->query->add([
            'city' => 'london',
        ]);

        $weatherServiceMock = Mockery::mock(Weather::class)
            ->shouldReceive('getAverageWeatherByCity')
            ->once()
            ->andReturn([
                'tempInCelsius' => 25.5,
                'windSpeed' => 10.2,
            ])
            ->getMock();

        $weatherController = new WeatherController($weatherServiceMock);
        $response = $weatherController->getAverageWeatherByCity($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(ResponseAlias::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('tempInCelsius', $responseData);
        $this->assertArrayHasKey('windSpeed', $responseData);
    }
}
