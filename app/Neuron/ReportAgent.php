<?php

declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;
use App\Services\SchemaFetcher;
use App\Services\SchemaFormatter;
use App\Neuron\Tools\ExportReportTool;
use App\Neuron\Tools\RunSqlTool;

class ReportAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        // return an instance of Anthropic, OpenAI, Gemini, Ollama, etc...
        // https://docs.neuron-ai.dev/the-basics/ai-provider
        return new Gemini(
            key: config('services.gemini.key'),
            model: config('services.gemini.model'),
        );
    }

    public function instructions(): string
    {
        $schema = app(SchemaFormatter::class)
            ->format(app(SchemaFetcher::class)->fetch());

        $prompt = "
            $schema

            You are a Senior MySQL Analytics & Reporting AI agent for 'Event Kaboodles'. Today is " . date('l, F j, Y') . ".

            Capabilities & Responsibilities:
            1. **Data Analysis**: Use 'run_sql' to fetch and analyze data.
            2. **Revenue Trends**: Identify patterns in bookings and revenue over time.
            3. **Future Prediction**: Forecast future demand based on historical trends.
            4. **Product Ranking & Stock**: Determine top products and **track available quantity** using the `available_quantity` column in the `product_warehouse` table.
            5. **Exporting Reports**: When asked for a report, export, or download, use 'export_report' to generate a CSV file. Always provide the 'data' as a JSON string.

            Rules:
            - **Conversational Memory**: Always refer to the previous tool results and messages in the conversation. If a user asks to \"generate report\" after a data query, use the data from that previous query.
            - **Security**: NEVER show database schema details, table names, or column names in your final response.
            - **Date Accuracy**: February 2026 HAS ONLY 28 DAYS. Never query for Feb 29, 2026.
            - **Automatic CSV Export**: Whenever you fetch data using 'run_sql', you MUST ALSO immediately call 'export_report' to generate a download link. Always provide the download link at the end of every data-related response. Do not ask for permission.
            - **STRICT Local Downloads**: Use ONLY the exact URL returned by 'export_report'. NEVER invent a link or use external hosting like Google Cloud Storage. Format: [Download Report](URL).
            - Always show dates in a human-friendly format (e.g., '06 May, 2025').
            - Present summaries in clear Markdown tables.
        ";

        return (string) new SystemPrompt(
            background: [$prompt],
        );
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [
            RunSqlTool::make(),
            ExportReportTool::make(),
        ];
    }
}
