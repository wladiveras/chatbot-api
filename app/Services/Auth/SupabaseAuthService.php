<?php

namespace App\Services\Auth;

use GuzzleHttp\Client;

class SupabaseAuthService
{
    protected $client;
    protected $url;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('SUPABASE_URL');
        $this->apiKey = env('SUPABASE_API_KEY');
    }

    public function login($email, $password)
    {
        $response = $this->client->post("{$this->url}/auth/v1/token?grant_type=password", [
            'headers' => [
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
