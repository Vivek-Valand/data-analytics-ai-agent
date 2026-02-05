<?php

declare(strict_types=1);

namespace App\Neuron\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Facades\Storage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class RunSqlTool extends Tool
{
    protected ?array $dbConfig = null;

    public function __construct(?array $dbConfig = null)
    {
        $this->dbConfig = $dbConfig;
        // Define Tool name and description
        parent::__construct(
            'run_sql',
            'Execute a safe, read-only SQL SELECT query',
        );
    }

    /**
     * Properties are the input arguments of the __invoke method.
     */
    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'sql',
                type: PropertyType::STRING,
                description: 'SQL query to run',
                required: true,
            ),
        ];
    }

    /**
     * Implementing the tool logic
     */
    public function __invoke(string $sql): array
    {
        Log::info('Executing SQL Tool: '.$sql);

        if (! Str::of($sql)->trim()->upper()->startsWith('SELECT')) {
            throw new InvalidArgumentException('Only SELECT queries are allowed for security reasons.');
        }

        $connection = DB::connection();

        if ($this->dbConfig) {
            if (isset($this->dbConfig['type']) && $this->dbConfig['type'] === 'sql_file') {
                $connectionName = 'dynamic_db_sql_' . ($this->dbConfig['id'] ?? 'temp');
                config(['database.connections.' . $connectionName => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                    'prefix' => '',
                ]]);
                $connection = DB::connection($connectionName);
                
                // Import the SQL file
                $sqlPath = $this->dbConfig['sql_file'];
                if (Storage::disk('local')->exists($sqlPath)) {
                    $fileContent = Storage::disk('local')->get($sqlPath);
                    $connection->unprepared($fileContent);
                }
            } else {
                $connectionName = 'dynamic_db_' . ($this->dbConfig['id'] ?? 'temp');
                config(['database.connections.' . $connectionName => [
                    'driver' => $this->dbConfig['connection'] ?? 'mysql',
                    'host' => $this->dbConfig['host'],
                    'port' => $this->dbConfig['port'],
                    'database' => $this->dbConfig['database'],
                    'username' => $this->dbConfig['username'],
                    'password' => $this->dbConfig['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]]);
                $connection = DB::connection($connectionName);
            }
        }

        return $connection->select($sql);
    }
}
