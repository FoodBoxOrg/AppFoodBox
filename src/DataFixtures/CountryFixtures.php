<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = new Client();
        $response = $client->request('GET', 'https://restcountries.com/v3.1/all');

        $countries = json_decode($response->getBody(), true);

        foreach ($countries as $countryData) {
            $country = new Country();
            $country->setName($countryData['name']['common']);
            $country->setUrlFlag($countryData['flags']['png']);
            $country->setCountryCode($countryData['cca2']);

            $manager->persist($country);
        }

        $manager->flush();
    }
}
