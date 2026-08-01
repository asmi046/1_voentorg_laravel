<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CdekService
{
    protected function baseUrl(): string
    {
        return rtrim(config('cdek.base_url'), '/');
    }

    public function getAccessToken(bool $refresh = false): ?string
    {
        if (! $refresh) {
            $cached = Cache::get('cdek.access_token');
            if ($cached && $cached['expires_at'] > now()->timestamp) {
                return $cached['access_token'];
            }
        }

        try {
            $response = Http::asForm()->post($this->baseUrl().'/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('cdek.login'),
                'client_secret' => config('cdek.password'),
            ]);
        } catch (ConnectionException $e) {
            Log::channel('pay')->error('CDEK auth connection error: '.$e->getMessage());

            return null;
        }

        if (! $response->successful()) {
            Log::channel('pay')->error('CDEK auth failed: '.$response->body());

            return null;
        }

        $data = $response->json();
        $ttl = (int) ($data['expires_in'] ?? 3600);

        Cache::put('cdek.access_token', [
            'access_token' => $data['access_token'],
            'expires_at' => now()->timestamp + $ttl - 60,
        ], now()->addSeconds($ttl));

        return $data['access_token'];
    }

    public function getCities(array $params = []): ?array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->get($this->baseUrl().'/location/cities', $params);
        } catch (ConnectionException $e) {
            Log::channel('pay')->error('CDEK cities connection error: '.$e->getMessage());

            return null;
        }

        if ($response->status() === 401) {
            $token = $this->getAccessToken(true);
            if (! $token) {
                return null;
            }

            $response = Http::withToken($token)
                ->get($this->baseUrl().'/location/cities', $params);
        }

        if (! $response->successful()) {
            Log::channel('pay')->error('CDEK cities failed: '.$response->body());

            return null;
        }

        return $response->json();
    }
}
