<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsChatMessage extends Model
{
    protected $fillable = ['chat_history_id', 'thread_id', 'role', 'content', 'meta'];

    protected $casts = [
        'meta' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($message) {
            if ($message->thread_id && !$message->chat_history_id) {
                $message->chat_history_id = (int) $message->thread_id;
            } elseif ($message->chat_history_id && !$message->thread_id) {
                $message->thread_id = (string) $message->chat_history_id;
            }
        });
    }

    public function chatHistory()
    {
        return $this->belongsTo(AnalyticsChatHistory::class, 'chat_history_id');
    }
}
