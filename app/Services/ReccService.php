<?php
namespace App\Services;

use  GuzzleHttp\Client;

class ReccService {
    protected $baseEndpoint;
    protected $client;

    public function __construct() {
        $this->baseEndpoint = "http://" . env("RECC_IP", "reccapi") . ":" . env("RECC_PORT", "5000");
        $this->client = new Client(['base_uri' => $this->baseEndpoint]);
    }

    public function createAutoPlaylist(int $userId) {
        $response = $this->client->request(
            "GET",
            "/api/my_playlist",
            [
                "query" => [
                    "user_id" => $userId
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        return json_decode($body);
    }

    public function createCustomPlaylist(array $song_ids, string $playlist_id) {
        $response = $this->client->request(
            "POST",
            "/api/create_playlist",
            [
                "json" => [
                    "song_ids" => $song_ids,
                    "playlist_id" => $playlist_id
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        return json_decode($body);
    }
}
