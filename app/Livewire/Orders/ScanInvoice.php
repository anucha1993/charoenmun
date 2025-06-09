<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderDeliverysModel;

class ScanInvoice extends Component
{
    public $scanInput = '';
    public ?OrderModel $order = null;
    public array $deliveredQtyMap = [];

    public function updatedScanInput()
    {
        // ค้นหา OrderDelivery จากเลขบิลย่อย
        $delivery = OrderDeliverysModel::with(['order.customer', 'order.items', 'order.deliveryAddress', 'order.deliveries.payments'])
            ->where('order_delivery_number', $this->scanInput)
            ->first();

        if ($delivery) {
            $this->order = $delivery->order;

            // สร้าง Map ของ qty ที่ส่งแล้วในแต่ละสินค้า
            $this->deliveredQtyMap = $this->order->deliveries
                ->flatMap->items
                ->groupBy('product_id')
                ->map(fn($group) => $group->sum('quantity'))
                ->toArray();
        } else {
            $this->order = null;
            $this->dispatch('notify', type: 'error', message: 'ไม่พบบิลย่อยนี้');
        }

        $this->scanInput = '';
    }

    public function render()
    {
        return view('livewire.orders.scan-invoice')->layout('layouts.horizontal', [
            'title' => 'แสดงบิลจากการสแกน'
        ]);
    }
}
