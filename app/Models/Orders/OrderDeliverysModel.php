<?php

namespace App\Models\Orders;

use App\Models\Orders\OrderModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\OrderDeliveryItems;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDeliverysModel extends Model
{
    use HasFactory;

    protected $table = 'order_deliveries';
    protected $fillable = [
        'order_id',
        'order_delivery_number',
        'delivery_date',
        'delivery_status',
        'payment_status',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }

    public function deliveryItems()
    {
        return $this->hasMany(OrderDeliveryItems::class, 'order_delivery_id');
    }
}
