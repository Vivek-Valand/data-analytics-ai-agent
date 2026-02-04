<?php

declare(strict_types=1);

namespace App\Neuron\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class ExportReportTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            'export_report',
            'Generate a CSV report from data and return a download link',
        );
    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'data',
                type: PropertyType::STRING,
                description: 'The data to export as CSV (JSON string of array of objects/associative arrays)',
                required: true,
            ),
            new ToolProperty(
                name: 'filename',
                type: PropertyType::STRING,
                description: 'Optional filename for the report (without extension)',
                required: false,
            ),
        ];
    }

    public function __invoke(string $data, ?string $filename = null): string
    {
        $data = json_decode($data, true);

        if (empty($data)) {
            return "No data provided to export or invalid JSON format.";
        }

        $filename = ($filename ?: 'report_'.now()->format('Ymd_His')).'.csv';
        $path = 'reports/'.$filename;

        // Ensure directory exists
        if (!Storage::disk('public')->exists('reports')) {
            Storage::disk('public')->makeDirectory('reports');
        }

        $handle = fopen('php://temp', 'r+');
        
        // Headers
        $headers = array_keys((array) $data[0]);
        fputcsv($handle, $headers);

        // Data rows
        foreach ($data as $row) {
            fputcsv($handle, (array) $row);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('public')->put($path, $csvContent);

        $url = asset('storage/reports/'.$filename);
        
        return "Report generated successfully. Download link: [Download {$filename}]({$url})";
    }
}
