<?php

namespace App\Services\AI;

use App\Services\BaseService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CloudflareService extends BaseService implements CloudflareServiceInterface
{
    protected $url;
    protected $request;
    protected $apiKey;
    protected $accountId;

    public function __construct()
    {

        $this->url = Config::get('services.cloudflare.url', 'https://api.cloudflare.com/client/v4/');
        $this->apiKey = Config::get('services.cloudflare.api_key');
        $this->accountId = Config::get('services.cloudflare.account_id');

        $this->request = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])
            ->timeout(60)
            ->acceptJson();
    }

    public function generateText($prompt, $prePrompt = '', $oldMessages = [], $settings = []): array|object
    {
        try {
            $messages = [];

            if ($prePrompt) {
                $messages[] = ['role' => 'system', 'content' => $prePrompt];
            }

            foreach ($oldMessages as $message) {
                $messages[] = $message;
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            $payload = [
                'messages' => $messages,
            ];

            if (isset($settings['temperature'])) {
                $payload['temperature'] = $settings['temperature'];
            }

            if (isset($settings['max_tokens'])) {
                $payload['max_tokens'] = $settings['max_tokens'];
            }

            if (isset($settings['top_p'])) {
                $payload['top_p'] = $settings['top_p'];
            }

            $response = $this->request->post("{$this->url}accounts/{$this->accountId}/ai/run/@cf/llama-3-8b-instruct", [
                $payload,
            ]);

            if ($response->successful()) {
                return $this->success(message: 'Conexão retornada com sucesso.', payload: $response->json());
            }

            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possivel retornar essa conexão.',
                code: 400
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }


    }
}
