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
        'order_delivery_subtotal',
        'order_delivery_vat',
        'order_delivery_discount',
        'order_delivery_grand_total',
        'order_delivery_enable_vat',
        'order_delivery_vat_included',
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
