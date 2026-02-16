<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Neuron\ReportAgent;
use App\Models\AnalyticsChatHistory;
use App\Models\AnalyticsChatMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\AssistantMessage;
use App\Neuron\History\ArrayChatHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use App\Services\MarkdownRenderer;
use App\Models\DatabaseConfiguration;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('analytics');
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        try {
            $chatId = $request->input('chat_id');
            $messages = [];

            if ($chatId) {
                // Ensure chat belongs to user
                AnalyticsChatHistory::where('user_id', Auth::id())->findOrFail($chatId);

                $dbMessages = AnalyticsChatMessage::where('chat_history_id', $chatId)->orderBy('created_at')->get();

                foreach ($dbMessages as $msg) {
                    if ($msg->role === 'user') {
                        $messages[] = new UserMessage($msg->content);
                    } else {
                        $messages[] = new AssistantMessage($msg->content);
                    }
                }
            }

            $history = (new ArrayChatHistory())->setMessages($messages);
            
            $dbConfig = null;
            $dbConfigId = $request->input('db_config');
            if ($dbConfigId && $dbConfigId !== 'default') {
                if (str_starts_with($dbConfigId, 'db:')) {
                    $id = explode(':', $dbConfigId)[1];
                    $dbConfig = \App\Models\DatabaseConfiguration::where('user_id', Auth::id())->find($id)?->toArray();
                } elseif (str_starts_with($dbConfigId, 'sql:')) {
                    $id = explode(':', $dbConfigId)[1];
                    $sqlConfig = \App\Models\SqlFileConfig::where('user_id', Auth::id())->find($id);
                    if ($sqlConfig) {
                        $dbConfig = [
                            'name' => $sqlConfig->name,
                            'sql_file' => $sqlConfig->file_path,
                            'type' => 'sql_file'
                        ];
                    }
                }
            }

            if ($dbConfig && !isset($dbConfig['type'])) {
                $availableDatabases = $this->listDatabases($dbConfig);
                if (!empty($availableDatabases)) {
                    $dbConfig['databases'] = $availableDatabases;
                    $selectedDatabase = $this->matchDatabaseSelection($request->message, $availableDatabases);
                    $currentDatabase = $dbConfig['database'] ?? null;

                    if ($selectedDatabase) {
                        $dbConfig['database'] = $selectedDatabase;
                        if (!empty($dbConfig['id'])) {
                            DatabaseConfiguration::where('id', $dbConfig['id'])
                                ->where('user_id', Auth::id())
                                ->update(['database' => $selectedDatabase]);
                        }
                    } elseif (empty($currentDatabase) || !in_array($currentDatabase, $availableDatabases, true)) {
                        $dbConfig['database'] = $availableDatabases[0];
                    }
                }
            }

            $agent = ReportAgent::make()
                ->withChatHistory($history)
                ->withDbConfig($dbConfig);

            $response = $agent->chat(new UserMessage($request->message));
            $content = $response->getContent();
            $aiContent = app(MarkdownRenderer::class)->toHtml($content);

            if ($chatId) {
                $chatHistory = AnalyticsChatHistory::where('user_id', Auth::id())->findOrFail($chatId);
            } else {
                $chatHistory = AnalyticsChatHistory::create([
                    'user_id' => Auth::id(),
                    'title' => substr($request->message, 0, 50)
                ]);
            }

            AnalyticsChatMessage::create([
                'chat_history_id' => $chatHistory->id,
                'role' => 'user',
                'content' => $request->message
            ]);

            AnalyticsChatMessage::create([
                'chat_history_id' => $chatHistory->id,
                'role' => 'assistant',
                'content' => $aiContent
            ]);

            return response()->json([
                'status' => 'success',
                'content' => $aiContent,
                'chat_id' => $chatHistory->id
            ]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            if ($e instanceof \Illuminate\Database\QueryException || str_contains($message, 'SQLSTATE')) {
                $message = "Database connection error. Please check your database configuration and credentials.";
            }
            return response()->json(['status' => 'error', 'message' => $message], 500);
        }
    }

    private function listDatabases(array $dbConfig): array
    {
        $driver = $dbConfig['connection'] ?? 'mysql';
        if ($driver !== 'mysql') {
            return [];
        }

        $connectionName = 'probe_db_' . ($dbConfig['id'] ?? uniqid());
        $baseConfig = [
            'driver' => $driver,
            'host' => $dbConfig['host'] ?? null,
            'port' => $dbConfig['port'] ?? null,
            'username' => $dbConfig['username'] ?? null,
            'password' => $dbConfig['password'] ?? null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        $candidates = array_filter([
            $dbConfig['database'] ?? null,
            'information_schema',
            'mysql',
        ]);

        foreach ($candidates as $candidate) {
            try {
                config(['database.connections.' . $connectionName => $baseConfig + ['database' => $candidate]]);
                $connection = DB::connection($connectionName);
                $rows = $connection->select('SHOW DATABASES');
                return array_values(array_map(function ($row) {
                    return $row->Database ?? $row->database ?? null;
                }, $rows));
            } catch (Exception $e) {
                continue;
            }
        }

        return [];
    }

    private function matchDatabaseSelection(string $message, array $availableDatabases): ?string
    {
        $messageLower = Str::lower($message);
        $matches = [];

        foreach ($availableDatabases as $database) {
            if (!$database) {
                continue;
            }
            if (Str::contains($messageLower, Str::lower($database))) {
                $matches[] = $database;
            }
        }

        if (count($matches) === 1) {
            return $matches[0];
        }

        return null;
    }

    public function loadHistory()
    {
        $histories = AnalyticsChatHistory::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'created_at']);
        return response()->json($histories);
    }

    public function loadMessages($id)
    {
        // Ensure chat belongs to user
        AnalyticsChatHistory::where('user_id', Auth::id())->findOrFail($id);

        $messages = AnalyticsChatMessage::where('chat_history_id', $id)
            ->orderBy('created_at')
            ->get();
        return response()->json($messages);
    }

    public function deleteChat($id)
    {
        $history = AnalyticsChatHistory::where('user_id', Auth::id())->findOrFail($id);
        $history->delete();
        return response()->json(['status' => 'success']);
    }

    public function renameChat(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $history = AnalyticsChatHistory::where('user_id', Auth::id())->findOrFail($id);
        $history->update(['title' => $request->title]);
        return response()->json(['status' => 'success']);
    }

    public function downloadReport($filename)
    {
        $path = storage_path('app/public/reports/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    }
}
