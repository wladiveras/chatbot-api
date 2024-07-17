<?php

namespace App\Services\Authentication;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class AuthenticationService implements AuthenticationServiceInterface
{
    private mixed $url;

    private mixed $key;

    private mixed $request;

    private mixed $callback_url;

    public function __construct()
    {
        $this->url = Config::get('supabase.url');
        $this->key = Config::get('supabase.key');
        $this->callback_url = Config::get('app.url');

        $this->request = Http::withHeaders([
            'apikey' => $this->key,
        ])
            ->acceptJson();
    }

    public function findUser(int|string $id) {}
}
