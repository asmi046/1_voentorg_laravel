<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CdekService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CdekController extends Controller
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

    public function deliveryPoints(Request $request, CdekService $cdek): JsonResponse
    {
        $params = array_filter([
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

        $points = $cdek->getDeliveryPoints($params);

        if (is_null($points)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось получить список пунктов выдачи',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $points,
        ]);
    }

    public function availableTariffs(Request $request, CdekService $cdek): JsonResponse
    {
        $data = [
            'from_location' => array_filter([
                'code' => $request->input('from_code'),
                'address' => $request->input('from_address'),
                'coordinates' => array_filter([
                    'latitude' => $request->input('from_lat'),
                    'longitude' => $request->input('from_lon'),
                ]),
            ]),
            'to_location' => array_filter([
                'code' => $request->input('to_code'),
                'address' => $request->input('to_address'),
                'coordinates' => array_filter([
                    'latitude' => $request->input('to_lat'),
                    'longitude' => $request->input('to_lon'),
                ]),
            ]),
            'packages' => $request->input('packages', []),
        ];

        $optionalParams = array_filter([
            'date' => $request->input('date'),
            'type' => $request->input('type'),
            'currency' => $request->input('currency'),
            'tariff_code' => $request->input('tariff_code'),
            'services' => $request->input('services'),
        ]);

        $data = array_merge($data, $optionalParams);

        $tariffs = $cdek->getAvailableTariffs($data);

        if (is_null($tariffs)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось получить список тарифов',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $tariffs,
        ]);
    }

    public function allTariffs(Request $request, CdekService $cdek): JsonResponse
    {
        $params = array_filter([
            'currency' => $request->input('currency'),
            'lang' => $request->input('lang'),
        ]);

        $tariffs = $cdek->getAllTariffs($params);

        if (is_null($tariffs)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось получить список всех тарифов',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $tariffs,
        ]);
    }

    public function bestPickupPointTariff(Request $request, CdekService $cdek): JsonResponse
    {
        $validated = $request->validate([
            'to_code' => ['required'],
            'weight' => ['required', 'numeric'],
        ]);

        $tariff = $cdek->getBestPickupPointTariff(
            $validated['to_code'],
            (float) $validated['weight'],
        );

        if (is_null($tariff)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось рассчитать стоимость доставки',
            ], 500);
        }

        if ($tariff === []) {
            return response()->json([
                'success' => false,
                'message' => 'Подходящий тариф не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tariff,
        ]);
    }

    public function bestTariffByMode(Request $request, CdekService $cdek): JsonResponse
    {
        $validated = $request->validate([
            'to_code' => ['required'],
            'weight' => ['required', 'numeric'],
            'delivery_mode' => ['required', 'integer'],
        ]);

        $tariff = $cdek->getBestTariffByMode(
            $validated['to_code'],
            (float) $validated['weight'],
            (int) $validated['delivery_mode'],
        );

        if (is_null($tariff)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось рассчитать стоимость доставки',
            ], 500);
        }

        if ($tariff === []) {
            return response()->json([
                'success' => false,
                'message' => 'Подходящий тариф не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tariff,
        ]);
    }
}
