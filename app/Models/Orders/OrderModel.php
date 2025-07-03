<?php

namespace App\Models\Orders;

use App\Models\Orders\OrderItemsModel;
use App\Models\customers\customerModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quotations\QuotationModel;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderModel extends Model
{
   use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id';
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
        return $this->belongsTo(customerModel::class, 'customer_id');
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

    public function updatePaymentStatus(): void
    {
        $total = $this->order_grand_total;
        $payments = $this->payments()->get();
        $confirmedAmount = $payments
            ->where('status', 'ชำระเงินแล้ว')
            ->sum('amount');
        $hasPendingSlip = $payments
            ->where('status', 'รอยืนยันยอด')
            ->isNotEmpty();
        if ($confirmedAmount > $total) {
            $this->payment_status = 'overpayment';
        } elseif ($confirmedAmount == $total) {
            $this->payment_status = 'paid';
        } elseif ($confirmedAmount > 0 && $confirmedAmount < $total) {
            $this->payment_status = 'partial';
        } elseif ($hasPendingSlip) {
            $this->payment_status = 'waiting_confirmation';
        } else {
            $this->payment_status = 'pending';
        }
        $this->save();
    }
    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id');
    }
    
    /**
     * ตรวจสอบว่าใบสั่งซื้อนี้มีการส่งของครบแล้วหรือไม่
     * โดยเปรียบเทียบจำนวนสินค้าที่สั่งในใบสั่งซื้อกับจำนวนที่ส่งไปแล้วทั้งหมด
     * 
     * @return bool
     */
    public function isDeliveryComplete(): bool
    {
        // ดึงข้อมูลรายการสินค้าในใบสั่งซื้อ
        $orderItems = $this->items()->get();
        
        // ดึงข้อมูลจำนวนสินค้าที่ส่งไปแล้วทั้งหมด
        $deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
            ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
            ->where('order_items.order_id', $this->id)
            ->select('order_items.id as order_item_id', \Illuminate\Support\Facades\DB::raw('SUM(order_delivery_items.quantity) as delivered'))
            ->groupBy('order_items.id')
            ->pluck('delivered', 'order_item_id')
            ->toArray();
        
        // ตรวจสอบว่าส่งครบหรือไม่
        foreach ($orderItems as $item) {
            $ordered = $item->quantity;
            $delivered = $deliveredQtyMap[$item->id] ?? 0;
            
            // ถ้าพบรายการใดที่ส่งไม่ครบ แสดงว่าการส่งยังไม่ครบ
            if ($delivered < $ordered) {
                return false;
            }
        }
        
        // ถ้าทุกรายการส่งครบ
        return true;
    }

    /**
     * อัพเดทสถานะการจัดส่งของ Order
     * - "open" (เปิดรายการ) = มีการเปิดใบส่งของแล้ว แต่ยังไม่สำเร็จหรืออยู่ระหว่างจัดส่ง
     * - "someproducts" (ส่งสินค้าบางส่วน) = มีการส่งของแล้วบางส่วนแต่ยังไม่ครบทั้งหมด หรืออยู่ระหว่างจัดส่ง
     * - "completed" (ส่งสินค้าครบแล้ว) = ดำเนินการเสร็จสมบูรณ์แล้ว
     *
     * @return string สถานะการจัดส่งปัจจุบัน
     */
    public function updateDeliveryStatus(): string
    {
        // ดึงข้อมูลการจัดส่งทั้งหมดของออร์เดอร์นี้
        $deliveries = $this->deliveries()->get();
        
        // ถ้าไม่มีการจัดส่งเลย
        if ($deliveries->isEmpty()) {
            $this->order_status = 'open'; // เปลี่ยนจาก 'pending' เป็น 'open' ตามค่าที่อนุญาตในฐานข้อมูล
            $this->save();
            return 'open';
        }
        
        // ตรวจสอบว่ามีการส่งของครบแล้วหรือไม่
        $isFullyDelivered = $this->isDeliveryComplete();
        
        // ตรวจสอบว่ามีการส่งของเสร็จสิ้นแล้วบางส่วนหรือไม่
        $hasAnyDelivered = $deliveries->contains(function ($delivery) {
            return $delivery->order_delivery_status === 'delivered' || 
                   $delivery->order_delivery_status === 'success';
        });
        
        // กำหนดสถานะการจัดส่ง
        if ($isFullyDelivered) {
            $status = 'completed'; // ส่งสินค้าครบแล้ว (เปลี่ยนจาก delivered เป็น completed)
        } elseif ($hasAnyDelivered) {
            $status = 'someproducts'; // ส่งสินค้าบางส่วน
        } else {
            $status = 'open'; // เปิดรายการ
        }
        
        // บันทึกสถานะลงในฐานข้อมูล
        $this->order_status = $status;
        $this->save();
        
        return $status;
    }
}
