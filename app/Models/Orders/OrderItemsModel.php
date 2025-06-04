<?php

namespace App\Models\Orders;

use App\Models\Orders\OrderModel;
use App\Models\products\ProductModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\OrderDeliveryItems;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItemsModel extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_type',
        'product_unit',
        'product_detail',
        'product_length',
        'product_weight',
        'product_calculation',
        'quantity',
        'unit_price',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    // เชื่อมไปยัง order_delivery_items
    public function deliveryItems()
    {
        return $this->hasMany(OrderDeliveryItems::class, 'order_item_id');
    }
}
