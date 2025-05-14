<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenRouterService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function askAI(string $prompt): ?string
    {
        $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'openai/gpt-3.5-turbo', // ou mistralai/mistral-7b, etc.
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant utile.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $data = $response->toArray(false);
        return $data['choices'][0]['message']['content'] ?? null;
    }
}
