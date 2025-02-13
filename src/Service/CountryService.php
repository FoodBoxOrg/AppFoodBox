<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryService
{
    private HttpClientInterface $client;
    private string $countryUrl;

    public function __construct(HttpClientInterface $client, string $countryApiUrl)    {
        $this->client = $client;
        $this->countryUrl = $countryApiUrl;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getFlag(string $ISO): string
    {
        try {
            $response = $this->client->request('GET', $this->countryUrl . '/countries/' . $ISO);
            $flagData = $response->toArray();
            $flagUrl = $flagData['url_flag'] ?? $flagData['urlFlag'] ?? null;
        } catch (\Exception $e) {
            $flagUrl = null;
        }

        return $flagUrl;
    }
}