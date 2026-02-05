<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlFileConfig extends Model
{
    protected $fillable = [
        'name',
        'file_path',
    ];
}
