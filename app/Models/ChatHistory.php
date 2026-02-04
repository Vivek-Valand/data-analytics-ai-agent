<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
