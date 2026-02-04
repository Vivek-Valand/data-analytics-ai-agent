<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Neuron\ReportAgent;
use App\Models\ChatHistory;
use App\Models\ChatMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\AssistantMessage;
use App\Neuron\History\ArrayChatHistory;
use Exception;
use App\Services\MarkdownRenderer;

class HomeController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable|exists:chat_histories,id'
        ]);

        try {
            $chatId = $request->chat_id;
            $messages = [];

            if ($chatId) {
                $dbMessages = ChatMessage::where(function($query) use ($chatId) {
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
            $agent = ReportAgent::make()->withChatHistory($history);

            // Get response WITHOUT saving yet
            $response = $agent->chat(new UserMessage($request->message));
            // $aiContent = $response->getContent();
            $content = $response->getContent();
            $aiContent = app(MarkdownRenderer::class)->toHtml($content);

            // If we get here, AI responded properly. Now persist.
            if ($chatId) {
                $chatHistory = ChatHistory::find($chatId);
            } else {
                $chatHistory = ChatHistory::create([
                    'title' => substr($request->message, 0, 30) . '...',
                    'user_id' => auth()->id()
                ]);
            }

            // Save User Message
            ChatMessage::create([
                'chat_history_id' => $chatHistory->id,
                'role' => 'user',
                'content' => $request->message
            ]);

            // Save Assistant Message
            ChatMessage::create([
                'chat_history_id' => $chatHistory->id,
                'role' => 'assistant',
                'content' => $aiContent
            ]);

            return response()->json([
                'status' => 'success',
                'content' => $aiContent,
                'chat_id' => $chatHistory->id,
                'title' => $chatHistory->title
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function loadHistory()
    {
        $histories = ChatHistory::latest()->get(['id', 'title', 'created_at']);
        return response()->json($histories);
    }

    public function loadMessages($id)
    {
        // Query by thread_id OR chat_history_id for backward compatibility
        $messages = ChatMessage::where(function($query) use ($id) {
            $query->where('thread_id', $id)
                  ->orWhere('chat_history_id', $id);
        })
            ->orderBy('created_at')
            ->get();
        return response()->json($messages);
    }

    public function deleteChat($id)
    {
        $history = ChatHistory::where('user_id', auth()->id())->findOrFail($id);
        $history->delete();
        return response()->json(['status' => 'success']);
    }

    public function renameChat(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $history = ChatHistory::where('user_id', auth()->id())->findOrFail($id);
        $history->update(['title' => $request->title]);
        return response()->json(['status' => 'success']);
    }
}
