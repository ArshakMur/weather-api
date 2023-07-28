<?php

namespace App\Http\Controllers;

use App\Services\Weather\Weather;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(title="Weather API", version="0.1")
 */
class WeatherController extends Controller
{
    public function __construct(protected Weather $weather){}

    /**
     * YourController description.
     *
     * @OA\Get(
     *     path="/api/weather/get-by-city",
     *     summary="get weather from different sources",
     *     tags={"Weather"},
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Name of city",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response description",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Service2", type="object",
     *                 @OA\Property(property="tempInCelsius", type="number", format="float"),
     *                 @OA\Property(property="windSpeed", type="number", format="float")
     *             ),
     *             @OA\Property(property="Service1", type="object",
     *                 @OA\Property(property="tempInCelsius", type="number", format="float"),
     *                 @OA\Property(property="windSpeed", type="number", format="float")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request response description",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="The city is required")
     *         )
     *     )
     * )
     * */
    public function getByCityName(Request $request): JsonResponse
    {
        if (!$request->has('city')) {
            return response()->json(['error' => 'The city is required'], 400);
        }

        return response()->json($this->weather->getWeatherByCity($request->get('city')), Response::HTTP_OK);
    }

    /**
     * YourController description.
     *
     * @OA\Get(
     *     path="/api/weather/get-average-by-city",
     *     summary="get weather average from different sources",
     *     tags={"Weather"},
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Name of city",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response description",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tempInCelsius", type="number", format="float"),
     *             @OA\Property(property="windSpeed", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request response description",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="The city is required")
     *         )
     *     )
     * )
     * */
    public function getAverageWeatherByCity(Request $request): Response
    {
        if (!$request->has('city')) {
            return response()->json(['error' => 'The city is required'], 400);
        }

        return response()
            ->json($this->weather->getAverageWeatherByCity($request->get('city')),
                Response::HTTP_OK
            );
    }
}
