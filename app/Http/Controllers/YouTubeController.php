<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Neuron\YouTubeAgent;
use NeuronAI\Chat\Messages\UserMessage;

class YouTubeController extends Controller
{
    public function index()
    {
        return view('youtube');
    }

    public function summarize(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        try {
            $agent = YouTubeAgent::make();
            $response = $agent->chat(
                new UserMessage("Summarize this video URL: " . $request->url)
            );
            return response()->json([
                'status' => 'success',
                'content' => $response->getContent()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
