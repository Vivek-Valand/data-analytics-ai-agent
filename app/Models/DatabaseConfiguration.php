<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseConfiguration extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'connection',
        'host',
        'port',
        'database',
        'username',
        'password',
    ];
}
