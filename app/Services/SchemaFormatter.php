<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

class SchemaFormatter
{
    public function format(array $schema): string
    {
        $grouped = collect($schema)->groupBy(function ($item) {
            return $item->table_name ?? $item->TABLE_NAME ?? $item['table_name'] ?? $item['TABLE_NAME'] ?? 'unknown';
        });

        $output = "Database schema (Tables and Columns):\n";

        foreach ($grouped as $table => $columns) {
            if ($table === 'unknown') continue;
            
            $cols = $columns->map(function ($c) {
                $name = $c->column_name ?? $c->COLUMN_NAME ?? $c['column_name'] ?? $c['COLUMN_NAME'] ?? 'unknown';
                $type = $c->data_type ?? $c->DATA_TYPE ?? $c['data_type'] ?? $c['DATA_TYPE'] ?? '';
                return "{$name} {$type}";
            })->implode(', ');

            $output .= "{$table}({$cols})\n";
        }

        return $output;
    }
}
