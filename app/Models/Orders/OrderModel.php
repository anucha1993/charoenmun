<?php

namespace App\Models\Orders;

use App\Models\Orders\OrderItemsModel;
use App\Models\customers\CustomerModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quotations\QuotationModel;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderModel extends Model
{
   use HasFactory;

    protected $table = 'orders';
    protected $fillable = [
        'quote_id',
        'order_number',
        'order_date',
        'customer_id',
        'delivery_address_id',
        'order_subtotal',
        'order_discount',
        'order_vat',
        'order_grand_total',
        'payment_status',
        'order_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    // ─── ความสัมพันธ์ ──────────────────────────

    public function quotation()
    {
        return $this->belongsTo(QuotationModel::class, 'quote_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(deliveryAddressModel::class, 'delivery_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItemsModel::class, 'order_id');
    }

    public function deliveries()
    {
        return $this->hasMany(OrderDeliverysModel::class, 'order_id');
    }
}
