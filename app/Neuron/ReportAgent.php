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
    protected ?array $dbConfig = null;

    public function withDbConfig(?array $config): self
    {
        $this->dbConfig = $config;
        return $this;
    }

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
            ->format(app(SchemaFetcher::class)->fetch($this->dbConfig));

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
            - **Error Handling**: If a database query fails, do not show the technical SQL error or technical details. Instead, suggest that there might be a connection issue or that the requested data is currently unavailable.
            - **Date Accuracy**: February 2026 HAS ONLY 28 DAYS. Never query for Feb 29, 2026.
            - Always show dates in a human-friendly format (e.g., '06 May, 2025').
            - Present summaries in clear Markdown tables.
            - **Conditional CSV Export**: Only generate a downloadable CSV report when the user explicitly requests it. Do not automatically call export_report after every run_sql or table display.
            - **STRICT Local Downloads**: When generating a report, use only the exact URL returned by export_report. Never invent a link or use external storage. Format the link as: [Download Report](URL).
            - **Table Display**: For large lists or tables, show them in the chat normally unless the user asks for a downloadable report.
            - **Table Display**: Do NOT display sensitive or payment-related fields in tables, including but not limited to: token, payment_id, stripe_payment_method_id, stripe_customer_id, or any other Stripe-related identifiers.
            - **Table Display**: If a data table contains many columns or extends beyond the visible viewport (e.g., additional columns are hidden off-screen), render the table inside a horizontally scrollable container so all columns are accessible without truncation. Ensure the table remains responsive and readable across devices.
            - **Large Result Sets**: If a result set has more than 15 records, summarize key insights and ask the user if they want a downloadable CSV. Only call export_report after they confirm.
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
            new RunSqlTool($this->dbConfig),
            ExportReportTool::make(),
        ];
    }
}
