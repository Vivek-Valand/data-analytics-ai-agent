<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function warehouses()
    {
        return $this->hasMany(ProductWarehouse::class);
    }
}
