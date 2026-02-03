<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenMeteoService
{
    public function __construct(private HttpClientInterface $client) {}

    /**
     * Récupère la météo par ville ou par coordonnées
     *
     * @param string|null $city
     * @param float|null $lat
     * @param float|null $lon
     * @return array
     */
    public function getWeather(?string $city = null, ?float $lat = null, ?float $lon = null): array
    {
        // Si la ville est fournie mais pas les coordonnées
        if ($city !== null && ($lat === null || $lon === null)) {
            $coords = $this->geocodeCity($city);
            if (!$coords) {
                return ['error' => 'Ville non trouvée'];
            }
            $lat = $coords['lat'];
            $lon = $coords['lon'];
        }

        // Si seulement lat/lon sont fournis, on récupère le nom de la ville
        if ($city === null && $lat !== null && $lon !== null) {
            $city = $this->reverseGeocode($lat, $lon) ?? 'Coordinates';
        }

        if ($lat === null || $lon === null) {
            return ['error' => 'Latitude et longitude requises'];
        }

        // Appel principal à Open-Meteo
        try {
            $response = $this->client->request('GET', 'https://api.open-meteo.com/v1/forecast', [
                'query' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current_weather' => true,
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max',
                    'timezone' => 'auto'
                ]
            ]);

            $data = $response->toArray();

            // Structure propre du retour
            return [
                'city' => $city,
                'latitude' => $lat,
                'longitude' => $lon,
                'current_weather' => $data['current_weather'] ?? [],
                'daily' => $data['daily'] ?? [],
            ];

        } catch (\Exception $e) {
            return ['error' => 'Impossible de récupérer la météo', 'details' => $e->getMessage()];
        }
    }

    /**
     * Géocode une ville pour obtenir latitude et longitude
     */
    private function geocodeCity(string $city): ?array
    {
        try {
            $response = $this->client->request('GET', 'https://geocoding-api.open-meteo.com/v1/search', [
                'query' => [
                    'name' => $city,
                    'count' => 1
                ]
            ]);

            $data = $response->toArray();
            if (!empty($data['results'][0])) {
                return [
                    'lat' => $data['results'][0]['latitude'],
                    'lon' => $data['results'][0]['longitude'],
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Reverse-geocode pour récupérer le nom d'une ville depuis lat/lon
     */
    private function reverseGeocode(float $lat, float $lon): ?string
    {
        try {
            $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/reverse', [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'format' => 'json',
                ],
                'headers' => [
                    'User-Agent' => 'MyWeatherApp/1.0', // Obligatoire pour Nominatim
                ]
            ]);

            $data = $response->toArray();
            $address = $data['address'] ?? [];

            // Priorité : city > town > village > state
            return $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['state'] ?? null;

        } catch (\Exception $e) {
            return null;
        }
    }
public function getCoordinatesFromCity(string $city): ?array
{
    return $this->geocodeCity($city);
}

public function getCityFromCoordinates(float $lat, float $lon): ?string
{
    return $this->reverseGeocode($lat, $lon);
}

}
