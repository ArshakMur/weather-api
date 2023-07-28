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


class RapidWeatherApi implements WeatherApiServiceInterface
{
    private Client $client;
    public function __construct()
    {
        $this->setClient();
    }

    private function setClient(): void
    {
        $this->client = new Client([
            'base_uri' => config('weather.rapid.base_url'),
            'headers' => [
                'X-RapidAPI-Key' => config('weather.rapid.api_key'),
                'Content-Type'   => 'application/json',
            ]
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function getWeatherByCity(string $city): WeatherResultDTO
    {

        $response = $this->client->request('GET', '/current.json',
            ['query' => ['q' => $city],
            'http_errors' => false
            ]
        );

        return $this->handleResponse($response);
    }


    /**
     * @throws InvalidAuth
     * @throws CityNotFound
     * @throws UnexpectedStatusCode
     */
    public function handleResponse(ResponseInterface $response): WeatherResultDTO
    {
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
            case Response::HTTP_OK:
                return $this->prepareData($response->getBody()->getContents());
            case Response::HTTP_FORBIDDEN:
                throw new InvalidAuth('Invalid api token');
            case Response::HTTP_BAD_REQUEST:
                throw new CityNotFound('City not found in');
            default:
                throw new UnexpectedStatusCode('Unexpected StatusCode');
        }
    }


    /**
     * @throws UnexpectedResponse
     * @throws \JsonException
     */
    public function prepareData(string $data): WeatherResultDTO
    {
        $content = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (array_key_exists('current', $content)) {
            $current  = $content['current'];

            return new WeatherResultDTO($current['temp_c'], $current['wind_kph']);
        }

        throw new UnexpectedResponse('unexpected response body');
    }

    public function getServiceName(): string
    {
        return 'Rapid Weather';
    }
}
