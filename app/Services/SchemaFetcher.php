<?php

declare(strict_types=1);

namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SchemaFetcher
{
    public function fetch(?array $config = null): array
    {
        $connection = DB::connection();

        if ($config) {
            if (isset($config['type']) && $config['type'] === 'sql_file') {
                $connectionName = 'dynamic_db_sql_' . ($config['id'] ?? 'temp');
                config(['database.connections.' . $connectionName => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                    'prefix' => '',
                ]]);
                $connection = DB::connection($connectionName);
                
                // Import the SQL file
                $sqlPath = $config['sql_file'];
                if (Storage::disk('local')->exists($sqlPath)) {
                    $sql = Storage::disk('local')->get($sqlPath);
                    $connection->unprepared($sql);
                }
            } else {
                $connectionName = 'dynamic_db_' . ($config['id'] ?? 'temp');
                config(['database.connections.' . $connectionName => [
                    'driver' => $config['connection'] ?? 'mysql',
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $config['database'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]]);
                $connection = DB::connection($connectionName);
            }
        }

        if (isset($config['type']) && $config['type'] === 'sql_file') {
            return $connection->select("
                SELECT m.name AS table_name, p.name AS column_name, p.type AS data_type
                FROM sqlite_master m
                JOIN pragma_table_info(m.name) p
                WHERE m.type = 'table'
                ORDER BY m.name, p.cid
            ");
        }

        return $connection->select("
            SELECT table_name, column_name, data_type
            FROM information_schema.columns
            WHERE table_schema = " . ($config ? "'" . $config['database'] . "'" : "DATABASE()") . "
            ORDER BY table_name, ordinal_position
        ");
    }
}
