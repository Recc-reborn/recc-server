<?php
namespace App\Services;

use GuzzleHttp\Client;

class LastFMService {
    protected $baseURI = 'http://ws.audioscrobbler.com/';
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiKey = config('services.last-fm.key');
        $this->client = new Client(['base_uri' => $this->baseURI]);
    }

    public function call(string $method, array $params)
    {
        $response = $this->client->request(
            'GET',
            '2.0/',
            [
                'query' => [
                    'api_key' => $this->apiKey,
                    'method' => $method,
                    'format' => 'json',
                    ...$params
                ],
            ]
        );

        $body = $response->getBody()->getContents();

        return json_decode($body);
    }
}
