<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPrint extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_delivery_id',
        'printed_by',
        'print_count',
        'printed_at',
        'is_complete_delivery',
        'approved_code'
    ];
    
    protected $casts = [
        'printed_at' => 'datetime',
        'is_complete_delivery' => 'boolean',
    ];
    
    public function delivery()
    {
        return $this->belongsTo(\App\Models\Orders\OrderDeliverysModel::class, 'order_delivery_id');
    }
}
