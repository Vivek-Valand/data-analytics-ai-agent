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
use Exception;
use App\Services\MarkdownRenderer;

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
                $dbMessages = AnalyticsChatMessage::where(function($query) use ($chatId) {
                    $query->where('thread_id', $chatId)
                          ->orWhere('chat_history_id', $chatId);
                })->orderBy('created_at')->get();

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
                    $dbConfig = \App\Models\DatabaseConfiguration::find($id)?->toArray();
                } elseif (str_starts_with($dbConfigId, 'sql:')) {
                    $id = explode(':', $dbConfigId)[1];
                    $sqlConfig = \App\Models\SqlFileConfig::find($id);
                    if ($sqlConfig) {
                        // For SQL files, we'll try to use a temporary SQLite connection
                        // This is a simplified version: we inform the agent about the file
                        $dbConfig = [
                            'name' => $sqlConfig->name,
                            'sql_file' => $sqlConfig->file_path,
                            'type' => 'sql_file'
                        ];
                    }
                }
            }

            $agent = ReportAgent::make()
                ->withChatHistory($history)
                ->withDbConfig($dbConfig);

            // Get response WITHOUT saving yet
            $response = $agent->chat(new UserMessage($request->message));
            // $aiContent = $response->getContent();
            $content = $response->getContent();
            $aiContent = app(MarkdownRenderer::class)->toHtml($content);

            // If we get here, AI responded properly. Now persist.
            if ($chatId) {
                $chatHistory = AnalyticsChatHistory::findOrFail($chatId);
            } else {
                $chatHistory = AnalyticsChatHistory::create([
                    'user_id' => Auth::id() ?? 1,
                    'title' => substr($request->message, 0, 50)
                ]);
            }

            // Save User Message
            AnalyticsChatMessage::create([
                'chat_history_id' => $chatHistory->id,
                'role' => 'user',
                'content' => $request->message
            ]);

            // Save Assistant Message
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

    public function loadHistory()
    {
        $histories = AnalyticsChatHistory::where('user_id', Auth::id() ?? 1)
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'title', 'updated_at']);
        return response()->json($histories);
    }

    public function loadMessages($id)
    {
        // Query by thread_id OR chat_history_id for backward compatibility
        $messages = AnalyticsChatMessage::where(function($query) use ($id) {
            $query->where('thread_id', $id)
                  ->orWhere('chat_history_id', $id);
        })
            ->orderBy('created_at')
            ->get();
        return response()->json($messages);
    }

    public function deleteChat($id)
    {
        $history = AnalyticsChatHistory::where('user_id', Auth::id() ?? 1)->findOrFail($id);
        $history->delete();
        return response()->json(['status' => 'success']);
    }

    public function renameChat(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $history = AnalyticsChatHistory::where('user_id', Auth::id() ?? 1)->findOrFail($id);
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
