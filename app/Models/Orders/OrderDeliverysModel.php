<?php

namespace App\Models\Orders;

use App\Models\User;
use App\Enums\TruckType;
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
        'total_weight_kg',
        'recommended_truck_type',
        'selected_truck_type',
        'truck_note',
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
        'recommended_truck_type' => TruckType::class,
        'selected_truck_type' => TruckType::class,
        'total_weight_kg' => 'decimal:2',
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

/**
     * คำนวณน้ำหนักรวมของการจัดส่งนี้ (kg)
     */
    public function calculateTotalWeight(): float
    {
        $totalWeight = 0;
        
        foreach ($this->deliveryItems as $deliveryItem) {
            $orderItem = $deliveryItem->orderItem;
            if ($orderItem && $orderItem->product_weight) {
                // น้ำหนัก = น้ำหนัก/หน่วย × จำนวนที่จัดส่ง × ความหนา × ความยาว
                $weight = (float)$orderItem->product_weight;
                $quantity = (float)$deliveryItem->quantity;
                $calculation = (float)($orderItem->product_calculation ?? 1);
                $length = (float)($orderItem->product_length ?? 1);
                
                $totalWeight += $weight * $quantity * $calculation * $length;
            }
        }
        
        return round($totalWeight, 2);
    }

    /**
     * อัปเดตน้ำหนักและแนะนำประเภทรถ
     */
    public function updateWeightAndTruckRecommendation(): void
    {
        $this->total_weight_kg = $this->calculateTotalWeight();
        $this->recommended_truck_type = TruckType::getRecommendedTruck($this->total_weight_kg);
        
        // ถ้ายังไม่เลือกรถ ให้ใช้รถที่แนะนำ
        if (!$this->selected_truck_type) {
            $this->selected_truck_type = $this->recommended_truck_type;
        }
        
        $this->save();
    }

    /**
     * ตรวจสอบว่าน้ำหนักเกินขีดจำกัดของรถที่เลือกหรือไม่
     */
    public function isOverweight(): bool
    {
        if (!$this->selected_truck_type) {
            return false;
        }
        
        $capacity = $this->selected_truck_type->capacity();
        return $this->total_weight_kg > $capacity['max'];
    }

    /**
     * ได้รถประเภทอื่นที่สามารถรับน้ำหนักนี้ได้
     */
    public function getAlternativeTrucks(): array
    {
        $alternatives = [];
        
        foreach (TruckType::getAllTrucks() as $truckType) {
            $capacity = $truckType->capacity();
            if ($this->total_weight_kg >= $capacity['min'] && $this->total_weight_kg <= $capacity['max']) {
                $alternatives[] = $truckType;
            }
        }
        
        return $alternatives;
    }

    /**
     * คำนวณจำนวนรอบที่ต้องขนส่ง (ถ้าน้ำหนักเกิน)
     */
    public function calculateRequiredTrips(): int
    {
        if (!$this->selected_truck_type) {
            return 1;
        }
        
        $capacity = $this->selected_truck_type->capacity();
        return (int)ceil($this->total_weight_kg / $capacity['max']);
    }

    /**
     * ข้อมูลสรุปการขนส่ง
     */
    public function getTransportSummary(): array
    {
        return [
            'total_weight_kg' => $this->total_weight_kg,
            'total_weight_ton' => round($this->total_weight_kg / 1000, 2),
            'recommended_truck' => $this->recommended_truck_type,
            'selected_truck' => $this->selected_truck_type,
            'is_overweight' => $this->isOverweight(),
            'required_trips' => $this->calculateRequiredTrips(),
            'alternative_trucks' => $this->getAlternativeTrucks(),
        ];
    }

}
