<?php

namespace Services;

use GuzzleHttp\Client;

class UnsplashService {
    private $client;
    private $accessKey;

    public function __construct() {
        $this->accessKey = getenv('UNSPLASH_ACCESS_KEY') ?: 'wY9XRjgKYGLHS7DdZAnSo5TkmV5GYrkcO1lu3epjzVo';
        $this->client = new Client([
            'base_uri' => 'https://api.unsplash.com/',
            'timeout'  => 5.0,
        ]);
    }

    public function getImage(string $keyword): ?string {
        try {
            $response = $this->client->request('GET', 'photos/random', [
                'query' => [
                    'query' => $keyword,
                    'orientation' => 'landscape',
                    'client_id' => $this->accessKey
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            return $data['urls']['regular'] ?? null;
        } catch (\Exception $e) {
            // In a real app, log this.
            // error_log("Unsplash Error: " . $e->getMessage());
            return null;
        }
    }
}
