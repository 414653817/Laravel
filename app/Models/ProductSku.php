<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = ['title', 'description', 'price', 'stock'];

    //和商品表关联(属于)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
