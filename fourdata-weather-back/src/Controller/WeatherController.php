<?php

namespace App\Controller;

use App\Service\OpenMeteoService;
use App\Entity\WeatherSearch;
use App\Repository\WeatherSearchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/api/weather', methods: ['GET'])]
    public function weather(Request $request, OpenMeteoService $service): JsonResponse
    {
        $city = $request->query->get('city');
        $lat = $request->query->get('lat');
        $lon = $request->query->get('lon');

        $lat = $lat !== null ? (float) $lat : null;
        $lon = $lon !== null ? (float) $lon : null;

        $weather = $service->getWeather($city, $lat, $lon);

        return $this->json($weather);
    }

    // #[Route('/api/favorites', methods: ['POST'])]
    // public function saveFavorite(Request $request, EntityManagerInterface $em): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $city = $data['city'] ?? null;
    //     $lat = isset($data['latitude']) ? (float)$data['latitude'] : null;
    //     $lon = isset($data['longitude']) ? (float)$data['longitude'] : null;

    //     // Vérification : soit city, soit lat/lon
    //     if (!$city && (!$lat || !$lon)) {
    //         return $this->json([
    //             'error' => 'Vous devez fournir soit une ville, soit latitude et longitude'
    //         ], 400);
    //     }

    //     try {
    //         $favorite = new WeatherSearch();
    //         $favorite->setCity($city);
    //         $favorite->setLatitude($lat);
    //         $favorite->setLongitude($lon);
    //         $favorite->setCreatedAt(new \DateTimeImmutable());

    //         $em->persist($favorite);
    //         $em->flush();

    //         // Renvoi propre JSON
    //         return $this->json([
    //             'id' => $favorite->getId(),
    //             'city' => $favorite->getCity(),
    //             'latitude' => $favorite->getLatitude(),
    //             'longitude' => $favorite->getLongitude(),
    //             'createdAt' => $favorite->getCreatedAt()->format(DATE_ATOM),
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return $this->json([
    //             'error' => 'Impossible d’ajouter le favori',
    //             'details' => $e->getMessage()
    //         ], 500);
    //     }
    // }
#[Route('/api/favorites', methods: ['POST'])]
public function saveFavorite(Request $request, EntityManagerInterface $em, OpenMeteoService $service): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $city = $data['city'] ?? null;
    $lat = isset($data['latitude']) ? (float)$data['latitude'] : null;
    $lon = isset($data['longitude']) ? (float)$data['longitude'] : null;

    // Vérification : soit city, soit lat/lon
    if (!$city && (!$lat || !$lon)) {
        return $this->json([
            'error' => 'Vous devez fournir soit une ville, soit latitude et longitude'
        ], 400);
    }

    try {
        // 1️⃣ Si on a la ville mais pas lat/lon, récupère depuis l'API
        if ($city && (!$lat || !$lon)) {
            $location = $service->getCoordinatesFromCity($city);
            if (!$location) {
                return $this->json(['error' => 'Impossible de récupérer les coordonnées pour cette ville'], 400);
            }
            $lat = $location['lat']; // attention ici les clés doivent correspondre à geocodeCity
            $lon = $location['lon'];
        }

        // 2️⃣ Si on a lat/lon mais pas la ville, récupère le nom
        if (($lat && $lon) && !$city) {
            $city = $service->getCityFromCoordinates($lat, $lon);
            if (!$city) {
                $city = 'Inconnue';
            }
        }

        // 3️⃣ Création de l'entité
        $favorite = new WeatherSearch();
        $favorite->setCity($city);
        $favorite->setLatitude($lat);
        $favorite->setLongitude($lon);
        $favorite->setCreatedAt(new \DateTimeImmutable());

        $em->persist($favorite);
        $em->flush();

        // 4️⃣ Retour JSON
        return $this->json([
            'id' => $favorite->getId(),
            'city' => $favorite->getCity(),
            'latitude' => $favorite->getLatitude(),
            'longitude' => $favorite->getLongitude(),
            'createdAt' => $favorite->getCreatedAt()->format(DATE_ATOM),
        ], 201);

    } catch (\Exception $e) {
        return $this->json([
            'error' => 'Impossible d’ajouter le favori',
            'details' => $e->getMessage()
        ], 500);
    }
}

    #[Route('/api/favorites', methods: ['GET'])]
    public function listFavorites(WeatherSearchRepository $repo): JsonResponse
    {
        $favorites = $repo->findAll();
        $result = [];

        foreach ($favorites as $fav) {
            $result[] = [
                'id' => $fav->getId(),
                'city' => $fav->getCity(),
                'latitude' => $fav->getLatitude(),
                'longitude' => $fav->getLongitude(),
                'createdAt' => $fav->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return $this->json($result);
    }

    #[Route('/api/favorites/{id}', methods: ['DELETE'])]
    public function deleteFavorite(WeatherSearch $favorite, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($favorite);
        $em->flush();

        return $this->json(null, 204);
    }
}
