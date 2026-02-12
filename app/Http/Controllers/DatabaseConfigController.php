<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DatabaseConfiguration;
use App\Models\SqlFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class DatabaseConfigController extends Controller
{
    public function index()
    {
        return view('db-config');
    }

    public function testConnection(Request $request)
    {
        $config = $request->all();
        
        try {
            // Temporarily set the connection configuration
            Config::set('database.connections.temp_test', [
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
            ]);

            DB::connection('temp_test')->getPdo();
            return response()->json(['success' => true, 'message' => 'Connection successful!']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $availableDatabases = $this->listDatabases($config);
            if (!empty($availableDatabases)) {
                $formatted = collect($availableDatabases)->map(function ($db) {
                    return "`{$db}`";
                })->implode(', ');
                $message = "Connection works, but the selected database was not found. Available databases: {$formatted}.";
                return response()->json(['success' => false, 'message' => $message]);
            }
            if ($e instanceof \Illuminate\Database\QueryException || str_contains($message, 'SQLSTATE')) {
                $message = "Database connection error. Please check your configuration and credentials.";
            }
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $message]);
        }
    }

    private function listDatabases(array $config): array
    {
        $driver = $config['connection'] ?? 'mysql';
        if ($driver !== 'mysql') {
            return [];
        }

        $connectionName = 'temp_probe';
        $baseConfig = [
            'driver' => $driver,
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'username' => $config['username'] ?? null,
            'password' => $config['password'] ?? null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        $candidates = array_filter([
            $config['database'] ?? null,
            'information_schema',
            'mysql',
        ]);

        foreach ($candidates as $candidate) {
            try {
                Config::set('database.connections.' . $connectionName, $baseConfig + ['database' => $candidate]);
                $connection = DB::connection($connectionName);
                $rows = $connection->select('SHOW DATABASES');
                return array_values(array_map(function ($row) {
                    return $row->Database ?? $row->database ?? null;
                }, $rows));
            } catch (\Exception $e) {
                continue;
            }
        }

        return [];
    }

    public function storeDatabaseConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'connection' => 'required|string',
                'host' => 'required|string',
                'port' => 'required|string',
                'database' => 'required|string',
                'username' => 'required|string',
                'password' => 'nullable|string',
            ]);

            DatabaseConfiguration::create($validated);

            return response()->json(['success' => true, 'message' => 'Database configuration saved!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save: ' . $e->getMessage()]);
        }
    }

    public function storeSqlFile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'sql_file' => 'required|file',
            ]);

            if ($request->hasFile('sql_file')) {
                $file = $request->file('sql_file');
                $path = $file->store('sql_imports', 'local');

                SqlFileConfig::create([
                    'name' => $request->name,
                    'file_path' => $path,
                ]);

                return response()->json(['success' => true, 'message' => 'SQL file uploaded and config saved!']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to upload: The sql file failed to upload.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to upload: The sql file failed to upload.']);
        }
    }

    public function getConfigs()
    {
        return response()->json([
            'databases' => DatabaseConfiguration::all(),
            'sqlFiles' => SqlFileConfig::all(),
        ]);
    }

    public function show($id)
    {
        return response()->json(DatabaseConfiguration::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'connection' => 'required|string',
                'host' => 'required|string',
                'port' => 'required|string',
                'database' => 'required|string',
                'username' => 'required|string',
                'password' => 'nullable|string',
            ]);

            $config = DatabaseConfiguration::findOrFail($id);
            $config->update($validated);

            return response()->json(['success' => true, 'message' => 'Database configuration updated!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update: ' . $e->getMessage()]);
        }
    }

    public function destroy($type, $id)
    {
        if ($type === 'db') {
            DatabaseConfiguration::findOrFail($id)->delete();
        } else {
            $config = SqlFileConfig::findOrFail($id);
            Storage::disk('local')->delete($config->file_path);
            $config->delete();
        }

        return response()->json(['success' => true, 'message' => 'Configuration deleted!']);
    }
}
