<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlFileConfig extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'file_path',
    ];
}
