<?php

namespace App\Models\products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
   use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_code','product_name','product_weight','product_price',
        'product_type','product_unit','product_note','product_status'
    ];

    protected $casts = [
        'product_status' => 'boolean',
        'product_weight' => 'decimal:2',
        'product_price'  => 'decimal:2',
    ];
}
