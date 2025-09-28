<?php

namespace App\Models\Orders;

use App\Models\Orders\OrderItemsModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\OrderDeliverysModel;
use App\Models\globalsets\GlobalSetValueModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDeliveryItems extends Model
{
     use HasFactory;

    protected $table = 'order_delivery_items';
    protected $fillable = [
        'order_delivery_id',
        'order_item_id',
        'quantity',
        'unit_price',
        'product_calculation',
        'product_note',
        'total',
    ];

    public function delivery()
    {
        return $this->belongsTo(OrderDeliverysModel::class, 'order_delivery_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItemsModel::class, 'order_item_id');
    }
        public function productMeasure()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_measure');
    }
}
