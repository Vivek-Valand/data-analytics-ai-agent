<?php

declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\Toolkits\Supadata\SupadataYouTubeToolkit;

class YouTubeAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new Gemini(
            key: config('services.gemini.key'),
            model: config('services.gemini.model'),
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: ["You are an AI Agent specialized in writing YouTube video summaries."],
            steps: [
                "Get the url of a YouTube video, or ask the user to provide one.",
                "Use the tools you have available to retrieve the transcription of the video.",
                "Write the summary.",
            ],
            output: [
                "Write a summary in a paragraph without using lists. Use just fluent text.",
                "After the summary add a list of three sentences as the three most important take away from the video.",
            ]
        );
    }

    protected function tools(): array
    {
        return [
            SupadataYouTubeToolkit::make(
                key: 'sd_f8bcbaddfb1478fbe282b781d8e6aff7',
            ),
        ];
    }
}
