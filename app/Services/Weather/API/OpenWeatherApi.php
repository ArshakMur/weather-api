<?php

namespace App\Services\Weather\API;

use App\Services\Weather\DTO\WeatherResultDTO;
use App\Services\Weather\Exceptions\CityNotFound;
use App\Services\Weather\Exceptions\InvalidAuth;
use App\Services\Weather\Exceptions\UnexpectedResponse;
use App\Services\Weather\Exceptions\UnexpectedStatusCode;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class OpenWeatherApi implements WeatherApiServiceInterface
{
    private Client $client;
    public function __construct()
    {
        $this->setClient();
    }

    private function setClient(): void
    {
        $this->client = new Client([
            'base_uri' => config('weather.open_weather.base_url'),
            'headers' => [
                'Content-Type'   => 'application/json',
            ]
        ]);
    }

    /**
     * @throws InvalidAuth
     * @throws CityNotFound
     * @throws UnexpectedStatusCode
     * @throws UnexpectedResponse
     * @throws \JsonException
     */
    private function handleResponse(ResponseInterface $response): WeatherResultDTO
    {
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
            case Response::HTTP_OK:
                return $this->prepareData($response->getBody()->getContents());
            case Response::HTTP_UNAUTHORIZED:
                throw new InvalidAuth('Invalid api token');
            case Response::HTTP_NOT_FOUND:
                throw new CityNotFound('City not found in');
            default:
                throw new UnexpectedStatusCode('Unexpected StatusCode');
        }
    }


    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function getWeatherByCity(string $city): WeatherResultDTO
    {

        $response = $this->client->request('GET', '/data/2.5/weather',
            [
                'query' => [
                    'q' => $city,
                    'appid' => config('weather.open_weather.api_key'),
                    'units' => 'metric',
                ],
                'http_errors' => false
            ]
        );

        return $this->handleResponse($response);
    }


    /**
     * @throws UnexpectedResponse
     * @throws \JsonException
     */
    private function prepareData(string $data): WeatherResultDTO
    {
        $content = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (array_key_exists('wind', $content) && array_key_exists('main', $content)) {
            return new WeatherResultDTO($content['main']['temp'], $content['wind']['speed']);
        }

        throw new UnexpectedResponse('Unexpected response body');
    }

    public function getServiceName(): string
    {
        return 'Open Weather';
    }
}
