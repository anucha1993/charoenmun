<?php

namespace App\Livewire\Orders;

use App\Models\DeliveryPrint;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\Orders\OrderDeliverysModel;
use Livewire\Component;

class OrderDeliveryPrint extends Component
{
    public OrderDeliverysModel $delivery;
    public bool $showPrintModal = false;
    public bool $showAuthCodeModal = false;
    public string $printedBy = '';
    public string $authCode = '';
    public string $errorMessage = '';
    public int $printCount = 0;
    public bool $isCompleteDelivery = false;

    public function mount(OrderDeliverysModel $delivery)
    {
        $delivery->load(['order', 'deliveryItems.orderItem', 'prints']);
        $this->delivery = $delivery;
        
        // ตรวจสอบจำนวนครั้งที่พิมพ์แล้ว
        $this->printCount = $delivery->prints()->count();
        
        // ตรวจสอบว่าส่งของครบหรือไม่
        $this->isCompleteDelivery = $delivery->isCompleteDelivery();
        
        // กำหนดชื่อผู้พิมพ์เริ่มต้นเป็นชื่อผู้ใช้ที่ login
        $this->printedBy = Auth::check() ? Auth::user()->name : '';
    }

    public function showPrintConfirmation()
    {
        // ถ้าพิมพ์น้อยกว่า 3 ครั้ง ให้แสดงหน้าต่างยืนยันการพิมพ์
        if ($this->printCount < 3) {
            $this->showPrintModal = true;
        } else {
            // ถ้าพิมพ์มากกว่าหรือเท่ากับ 3 ครั้งแล้ว ให้แสดงหน้าต่างใส่รหัสยืนยัน
            $this->showAuthCodeModal = true;
        }
    }

    public function verifyAuthCode()
    {
        // รหัสยืนยันที่กำหนดไว้ใน .env
        $correctCode = env('PRINT_AUTH_CODE', '5168');
        
        if ($this->authCode === $correctCode) {
            // รหัสถูกต้อง ให้แสดงหน้าต่างยืนยันการพิมพ์
            $this->showAuthCodeModal = false;
            $this->showPrintModal = true;
            $this->errorMessage = '';
        } else {
            // รหัสไม่ถูกต้อง
            $this->errorMessage = 'รหัสยืนยันไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
        }
    }

    public function confirmPrint()
    {
        if (empty($this->printedBy)) {
            $this->errorMessage = 'กรุณาระบุชื่อผู้พิมพ์';
            return;
        }
        
        // บันทึกประวัติการพิมพ์
        DeliveryPrint::create([
            'order_delivery_id' => $this->delivery->id,
            'printed_by' => $this->printedBy,
            'print_count' => $this->printCount + 1,
            'is_complete_delivery' => $this->isCompleteDelivery
        ]);
        
        // ปิดหน้าต่างยืนยัน
        $this->showPrintModal = false;
        $this->errorMessage = '';
        
        // ส่งคำสั่ง JavaScript เพื่อพิมพ์เอกสาร
        $this->dispatch('printDelivery');
    }

    #[Layout('layouts.horizontal-print')]
    public function render()
    {
        return view('livewire.orders.order-delivery-print', ['title' => 'Print Order Delivery']);
    }
}
