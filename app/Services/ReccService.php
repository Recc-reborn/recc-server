<?php
namespace App\Services;

use  GuzzleHttp\Client;

class ReccService {
    protected $baseEndpoint;
    protected $client;

    public function __construct()
    {
        $this->baseEndpoint = "http://" . env("RECC_IP", "reccapi") . ":" . env("RECC_PORT", "5000");
        $this->client = new Client(['base_uri' => $this->baseEndpoint]);
    }

    public function createPlaylist(array $ids, string $httpMethod = "POST")
    {
        $response = $this->client->request(
            $httpMethod,
            "/create_playlist",
            [
                "form_params" => $ids
            ]
        );
    }
}
