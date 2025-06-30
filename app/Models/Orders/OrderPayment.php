<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'user_id',
        'slip_path',
        'amount',
        'reference_id',
        'trans_ref',
        'sender_name',
        'receiver_name',
        'bank_name',
        'transfer_at',
        'status',
        'sender_account_no',
        'receiver_account_no'
    ];
    
    protected $casts = [
        'transfer_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
}
