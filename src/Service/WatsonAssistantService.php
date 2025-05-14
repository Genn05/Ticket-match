<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WatsonAssistantService
{
    private string $apiKey;
    private string $apiUrl;
    private HttpClientInterface $httpClient;

    public function __construct(string $apiKey, string $apiUrl, HttpClientInterface $httpClient)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->httpClient = $httpClient;
    }

    public function sendMessage(string $assistantId, string $message): array
    {
        $response = $this->httpClient->request('POST', $this->apiUrl . "/v2/assistants/" . $assistantId . "/sessions", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'input' => [
                    'message_type' => 'text',
                    'text' => $message,
                ],
            ],
        ]);

        return $response->toArray();
    }
}
