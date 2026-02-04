<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    protected $table = 'product_warehouse';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
