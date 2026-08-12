<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CdekService;
use App\Services\DeliveryCoordinator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function cities(Request $request, CdekService $cdek): JsonResponse
    {
        $params = array_filter([
            'country_codes' => $request->input('country_codes'),
            'region_code' => $request->input('region_code'),
            'city' => $request->input('city'),
            'page' => $request->input('page'),
            'size' => $request->input('size'),
        ]);

        $cities = $cdek->getCities($params);

        if (is_null($cities)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось получить список городов',
            ], 500);
        }

        $cityNames = array_column($cities, 'city');
        $localCities = City::whereIn('name', $cityNames)->get()->keyBy('name');

        foreach ($cities as &$city) {
            $cityName = $city['city'] ?? null;
            if ($cityName && isset($localCities[$cityName])) {
                $city['latitude'] = $localCities[$cityName]->latitude;
                $city['longitude'] = $localCities[$cityName]->longitude;
            }
        }
        unset($city);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function pickupPoints(Request $request, DeliveryCoordinator $coordinator): JsonResponse
    {
        $context = array_filter([
            'city_code' => $request->input('city_code'),
            'city' => $request->input('city'),
            'region_code' => $request->input('region_code'),
            'country_code' => $request->input('country_code'),
            'type_code' => $request->input('type_code'),
            'has_cashless' => $request->input('has_cashless'),
            'has_cash' => $request->input('has_cash'),
            'is_dressing' => $request->input('is_dressing'),
            'allowed_cod' => $request->input('allowed_cod'),
            'weight' => $request->input('weight'),
            'page' => $request->input('page'),
            'size' => $request->input('size'),
        ]);

        $points = $coordinator->getPickupPoints($context);

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $points,
            ],
        ]);
    }

    public function courierOffers(Request $request, DeliveryCoordinator $coordinator): JsonResponse
    {
        $validated = $request->validate([
            'to_code' => ['required'],
            'weight' => ['required', 'numeric'],
            'delivery_mode' => ['sometimes', 'integer'],
        ]);

        $offers = $coordinator->getCourierOffers([
            'to_code' => $validated['to_code'],
            'weight' => (float) $validated['weight'],
            'delivery_mode' => (int) ($validated['delivery_mode'] ?? 3),
        ]);

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }
}
