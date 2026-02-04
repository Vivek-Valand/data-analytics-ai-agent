<?php

declare(strict_types=1);

namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SchemaFetcher
{
    const ALLOWED_TABLES = ['orders','order_items','products','product_warehouse','users','chat_messages','chat_histories','analytics_chat_messages','analytics_chat_histories'];
    // const CONNECTION = 'mysql';

    public function fetch(): array
    {
        return DB::connection()->select("
            SELECT table_name, column_name, data_type
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
            AND table_name IN ('".implode("','", self::ALLOWED_TABLES)."')
            ORDER BY table_name, ordinal_position
        ");
    }
}
