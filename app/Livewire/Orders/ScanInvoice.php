<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderDeliverysModel;

class ScanInvoice extends Component
{
    public $scanInput = '';
    public $delivery = null;
    public ?OrderModel $order = null;

    public function updatedScanInput()
    {
        $delivery = OrderDeliverysModel::with(['order', 'payments'])->where('order_delivery_number', $this->scanInput)->first();
    
        if (!$delivery) {
            $this->order = null;
            $this->delivery = null;
            $this->dispatch('notify', type: 'error', message: 'ไม่พบบิลย่อยนี้');
        } else {
            $this->delivery = $delivery;
            $this->order = $delivery->order;
            $this->dispatch('notify', type: 'success', message: 'พบข้อมูลบิล ' . $this->scanInput);
        }
    
        // ✅ ล้างค่าหลังสแกน เพื่อให้ยิงต่อเนื่องได้
        $this->scanInput = '';
    }
    


    public function render()
    {
        if ($this->order) {
            $this->dispatch('show-invoice-modal'); // ✅ Trigger modal หลัง render
        }

        return view('livewire.orders.scan-invoice')
            ->layout('layouts.horizontal', ['title' => 'สแกนบิล']);
    }
}
