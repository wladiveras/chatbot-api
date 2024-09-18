<?php

namespace App\Services\AI;


interface CloudflareServiceInterface
{
    public function generateText($prompt, $prePrompt = '', $oldMessages = [], $settings = []): array|object;

}
