<?php
namespace App\Services;

use GuzzleHttp\Client;

class LastFMService {
    protected $baseURI = 'http://ws.audioscrobbler.com/2.0/';
    protected $apiKey = config('services.last-fm.key');
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseURI]);
    }

    public function call(string $method, array $params)
    {
        const $response = $this->client->request(
            'GET',
            '/',
            [
                'api_key' => $this->apikey,
                'method' => $method,
                'format' => 'json',
                ...$params,
            ]
        );

        return json_decode($response);
    }
}
