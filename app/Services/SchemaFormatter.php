<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

class SchemaFormatter
{
    public function format(array $schema): string
    {
        $grouped = collect($schema)->groupBy('TABLE_NAME');
        $output = "Database schema:\n";

        foreach ($grouped as $table => $columns) {
            /** @var Collection $columns */
            $cols = $columns
                ->map(fn ($c) => "{$c->COLUMN_NAME} {$c->DATA_TYPE}")
                ->implode(', ');

            $output .= "{$table}({$cols})\n";
        }

        return $output;
    }
}
