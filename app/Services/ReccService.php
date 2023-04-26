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

    public function createCustomPlaylist(string $song_ids, int $playlist_id)
    {
        $response = $this->client->request(
            "GET",
            "/api/create_playlist",
            [
                "query" => [
                    "song_ids" => $song_ids,
                    "playlist_id" => $playlist_id
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        return json_decode($body);
    }
}
