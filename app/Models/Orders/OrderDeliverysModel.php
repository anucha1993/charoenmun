<?php

namespace App\Models\Orders;

use App\Models\User;
use App\Models\Orders\OrderModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDeliverysModel extends Model
{
    use HasFactory;

    protected $table = 'order_deliveries';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'order_delivery_number',
        'delivery_address_id',
        'order_delivery_date',
        'order_delivery_status', // สถานะการจัดส่ง 0 = รอจัดส่ง , 1 = จัดส่งสำเร็จ , 2 =  ยกเลิกการจัดส่ง
        'payment_status', // สถานะการชำระเงิน 0 = รอชำระ , 1 = ชำระเงินครบแล้ว , 2 = ชำระเงินมัดจำ
        'order_delivery_status_order', //สถานะการส่งทั้งหมดของ order ว่าบิลไหนจัดส่งครบเป็นบิลสุดท้าย  1 = ส่งครบแล้ว , 0 = ''
        'order_delivery_note',
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
        'order_delivery_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
    public function deliveryAddress()
    {
        return $this->belongsTo(deliveryAddressModel::class, 'delivery_address_id');
    }

    public function deliveryItems()
    {
        return $this->hasMany(OrderDeliveryItems::class, 'order_delivery_id');
    }

    public function sale()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_delivery_id');
    }

    public function updatePaymentStatus(): void
{
    $confirmedAmount = $this->payments()
        ->where('status', 'ชำระเงินแล้ว')
        ->sum('amount');

    $total = $this->order_delivery_grand_total;

    $hasPendingSlip = $this->payments()
        ->where('status', 'รอยืนยันยอด')
        ->exists();

    if ($confirmedAmount > $total) {
        $this->payment_status = 'overpayment'; // ✅ มากกว่าต้องมาก่อน
    } elseif ($confirmedAmount == $total) {
        $this->payment_status = 'paid'; // ✅ พอดี
    } elseif ($confirmedAmount > 0 && $confirmedAmount < $total) {
        $this->payment_status = 'partial'; // ✅ จ่ายบางส่วน
    } elseif ($hasPendingSlip) {
        $this->payment_status = 'waiting_confirmation'; // ✅ แจ้งชำระแต่ยังไม่ยืนยัน
    } else {
        $this->payment_status = 'pending'; // ✅ ยังไม่จ่าย
    }

    $this->save();
}

    
    
}
