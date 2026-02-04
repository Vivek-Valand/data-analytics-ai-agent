<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsChatHistory extends Model
{
    protected $fillable = ['user_id', 'title'];

    public function messages()
    {
        return $this->hasMany(AnalyticsChatMessage::class, 'chat_history_id');
    }
}
