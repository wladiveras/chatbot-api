<?php

namespace App\Http\Controllers;

use App\Services\AI\CloudflareService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(CloudflareService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $prePrompt = $request->input('pre_prompt', '');
        $oldMessages = $request->input('old_messages', []);
        $settings = $request->input('settings', []);

        $generatedText = $this->aiService->generateText($prompt, $prePrompt, $oldMessages, $settings);

        return response()->json(['generated_text' => $generatedText]);
    }
}
