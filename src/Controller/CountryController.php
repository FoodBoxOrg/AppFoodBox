<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    #[Route('/api/flag/{countryCode}', name: 'get_country_flag', methods: ['GET'])]
    public function getFlag(CountryRepository $countryRepository, string $countryCode): JsonResponse
    {
        $urlFlag = $countryRepository->findFlagByCountryCode($countryCode);

        if (!$urlFlag) {
            return new JsonResponse(['error' => 'Drapeau non trouvé'], 404);
        }

        return new JsonResponse(['country_code' => $countryCode, 'flag_url' => $urlFlag]);
    }
}
