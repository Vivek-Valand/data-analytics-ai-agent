<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseConfiguration extends Model
{
    protected $fillable = [
        'name',
        'connection',
        'host',
        'port',
        'database',
        'username',
        'password',
    ];
}
