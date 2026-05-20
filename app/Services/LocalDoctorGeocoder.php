<?php

namespace App\Services;

use App\Models\LocalDoctor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocalDoctorGeocoder
{
    /**
     * 주소 필드(시/도·시군구·도로명·상세)를 합쳐 지오코딩 검색어로 만든다.
     */
    public function buildQuery(LocalDoctor $doctor): string
    {
        $parts = array_filter([
            trim((string) ($doctor->sido ?? '')),
            trim((string) ($doctor->sigungu ?? '')),
            trim((string) ($doctor->address ?? '')),
            trim((string) ($doctor->address_detail ?? '')),
        ]);

        return implode(' ', $parts);
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public function geocode(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        $apiKey = (string) config('local_doctor_map.kakao.rest_api_key', '');
        if ($apiKey === '') {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'KakaoAK ' . $apiKey,
        ])->get('https://dapi.kakao.com/v2/local/search/address.json', [
            'query' => $query,
        ]);

        if (! $response->successful()) {
            Log::warning('local_doctor.geocode_failed', [
                'query' => $query,
                'status' => $response->status(),
            ]);

            return null;
        }

        $document = $response->json('documents.0');
        if (! is_array($document) || ! isset($document['y'], $document['x'])) {
            return $this->geocodeKeyword($query, $apiKey);
        }

        return [
            'lat' => (float) $document['y'],
            'lng' => (float) $document['x'],
        ];
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function geocodeKeyword(string $query, string $apiKey): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'KakaoAK ' . $apiKey,
        ])->get('https://dapi.kakao.com/v2/local/search/keyword.json', [
            'query' => $query,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $document = $response->json('documents.0');
        if (! is_array($document) || ! isset($document['y'], $document['x'])) {
            return null;
        }

        return [
            'lat' => (float) $document['y'],
            'lng' => (float) $document['x'],
        ];
    }

    public function syncForDoctor(LocalDoctor $doctor): bool
    {
        $coords = $this->geocode($this->buildQuery($doctor));
        if ($coords === null) {
            $doctor->map_lat = null;
            $doctor->map_lng = null;
            $doctor->save();

            return false;
        }

        $doctor->map_lat = $coords['lat'];
        $doctor->map_lng = $coords['lng'];
        $doctor->save();

        return true;
    }
}
