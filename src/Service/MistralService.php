<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MistralService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $apiUrl;

    public function __construct(HttpClientInterface $client, string $mistralApiKey, string $mistralApiUrl)    {
        $this->client = $client;
        $this->apiKey = $mistralApiKey;
        $this->apiUrl = $mistralApiUrl;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function generateText(string $prompt): string
    {
        $response = $this->client->request('POST', $this->apiUrl . '/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'model' => 'mistral-large-latest',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $responseJson = $response->toArray();
        return $responseJson['choices'][0]['message']['content'];
    }
}