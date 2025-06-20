<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\Orders\OrderDeliverysModel;

class OrderShow extends Component
{
    public OrderModel $order;
    public array $deliveredQtyMap = [];
    public OrderDeliverysModel $delivery;

    public function mount(OrderModel $order)
    {
        $this->order = $order->load(['customer', 'deliveryAddress', 'items', 'deliveries.deliveryItems']);
        $this->order = $order->load([
            'deliveries.payments' // โหลดทุกบิลย่อยพร้อมรายการชำระ
        ]);

        // 🔽 สร้าง Map ของ product_id → จำนวนที่ถูกส่งไปแล้ว
        $this->deliveredQtyMap = OrderDeliveryItems::query()->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')->where('order_items.order_id', $order->id)->select('order_items.product_id', DB::raw('SUM(order_delivery_items.quantity) as delivered'))->groupBy('order_items.product_id')->pluck('delivered', 'product_id')->toArray();
    }
    public function render()
    {
        return view('livewire.orders.order-show')->layout('layouts.horizontal', ['title' => 'Order #' . $this->order->order_number]);
    }

    #[On('payment-created')]
    public function refreshData()
    {
        $this->order = $this->order->fresh(['deliveries.payments']); // ดึงข้อมูลใหม่
        // $this->dispatch('close-payment-modal');
    }

    /**
     * กดปุ่มสร้างรอบจัดส่งใหม่ (Delivery)
     * → clone ข้อมูลจาก order_items
     */

    public function createNewDelivery()
    {
        // เมื่อผู้ใช้กดปุ่ม “สร้างรอบจัดส่งใหม่” → redirect ไปหน้าฟอร์มสร้างรอบจัดส่ง
        return redirect()->route('deliveries.create', $this->order->id);
    }

    private function generateOrderDeliveryNumber(): string
    {
        $prefix = 'ODL' . now()->format('ym');
        $last = OrderDeliverysModel::where('order_delivery_number', 'like', $prefix . '%')
            ->orderBy('order_delivery_number', 'desc')
            ->value('order_delivery_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }


    public function updateDeliveryStatus($deliveryId, $status)
{
    $delivery = OrderDeliverysModel::find($deliveryId);

    if (!$delivery) {
        $this->dispatch('notify', type: 'error', message: 'ไม่พบบิลย่อย');
        return;
    }

    $delivery->order_delivery_status = $status;
    $delivery->save();

    $this->dispatch('notify', type: 'success', message: 'อัปเดตสถานะจัดส่งแล้ว');
}


    /**
     * กดปุ่มยืนยันจัดส่งในรอบนี้ → delivery_status = 'delivered'
     */
    public function markDeliveryAsDelivered($deliveryId)
    {
        $delivery = OrderDeliverysModel::findOrFail($deliveryId);

        if ($delivery->delivery_status !== 'pending') {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'รอบจัดส่งนี้ไม่สามารถเปลี่ยนสถานะได้',
            ]);
            return;
        }

        $delivery->update([
            'delivery_status' => 'delivered',
            'updated_by' => Auth::id(),
        ]);

        $this->order->load('deliveries.deliveryItems');

        $this->dispatchBrowserEvent('notify', [
            'type' => 'success',
            'message' => 'ยืนยันว่ารอบจัดส่ง #' . $delivery->order_delivery_number . ' จัดส่งเรียบร้อย',
        ]);

        // ถ้าออร์เดอร์หลักไม่มี order_items เหลือแล้ว สถานะจะเปลี่ยนเป็น completed (ทำไว้ใน createNewDelivery)
    }
}
