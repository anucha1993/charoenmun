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
    public $showPaymentForm = false;
    public $slip;
    public $amount;
    public $sender_name;
    public $transfer_at;

    public $newItems = [];
    public $addRowMode = false;
    public $addRowReason = null;
    public $addRowNote = '';

    public $order_subtotal = 0;
    public $order_vat = 0;
    public $order_discount = 0;
    public $order_grand_total = 0;
    public $order_enable_vat = false;
    public $order_vat_included = false;

    public function mount(OrderModel $order)
    {
        $this->order = $order->load(['customer', 'deliveryAddress', 'items', 'deliveries.deliveryItems', 'payments']);
        // 🔽 สร้าง Map ของ order_item_id → จำนวนที่ถูกส่งไปแล้ว
        $this->deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
            ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.id as order_item_id', \DB::raw('SUM(order_delivery_items.quantity) as delivered'))
            ->groupBy('order_items.id')
            ->pluck('delivered', 'order_item_id')
            ->toArray();
        // ดึงค่าตั้งต้นจาก order model
        $this->order_subtotal = $order->order_subtotal ?? 0;
        $this->order_vat = $order->order_vat ?? 0;
        $this->order_discount = $order->order_discount ?? 0;
        $this->order_grand_total = $order->order_grand_total ?? 0;
        $this->order_enable_vat = $order->order_enable_vat ?? false;
        $this->order_vat_included = $order->order_vat_included ?? false;
        // คำนวณยอดรวมล่าสุดจาก items ปัจจุบัน
        $this->calculateTotals();
    }
    public function render()
    {
        return view('livewire.orders.order-show')->layout('layouts.horizontal', ['title' => 'Order #' . $this->order->order_number]);
    }

    #[On('payment-created')]
    public function refreshData()
    {
        $this->order = $this->order->fresh(['payments', 'deliveries.deliveryItems']); // ดึงข้อมูลใหม่
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

    public function openPaymentModal($orderId)
    {
        $this->dispatch('open-payment-modal', $orderId);
    }

    public function submitPayment()
    {
        $this->validate([
            'slip' => 'required|image|max:2048',
            'amount' => 'required|numeric|min:1',
            'sender_name' => 'required|string',
            'transfer_at' => 'required|date',
        ]);
        $storedPath = $this->slip->store('payments', 'public');
        \App\Models\Orders\OrderPayment::create([
            'order_id' => $this->order->id,
            'user_id' => \Auth::id(),
            'slip_path' => $storedPath,
            'amount' => $this->amount,
            'sender_name' => $this->sender_name,
            'transfer_at' => $this->transfer_at,
            'status' => 'รอยืนยันยอด',
        ]);
        $this->showPaymentForm = false;
        $this->reset(['slip','amount','sender_name','transfer_at']);
        $this->dispatch('payment-created');
        session()->flash('success', 'แจ้งชำระเงินเรียบร้อยแล้ว');
    }

    public function addRow()
    {
        $this->newItems[] = [
            'product_id' => null,
            'product_search' => '',
            'product_name' => '',
            'product_type' => '',
            'product_unit' => '',
            'product_detail' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
            'product_vat' => false,
            'added_reason' => null,
            'added_note' => '',
            'product_results' => [],
            'product_results_visible' => false,
            'selected_from_dropdown' => false,
        ];
    }

    public function updatedNewItems($value, $key)
    {
        if (preg_match('/(\d+)\.product_search/', $key, $matches)) {
            $index = $matches[1];
            if (!empty($value)) {
                if (isset($this->newItems[$index]['product_id']) && !empty($this->newItems[$index]['product_id'])) {
                    $this->newItems[$index]['selected_from_dropdown'] = false;
                }
                $results = \App\Models\products\ProductModel::where('product_name', 'like', "%{$value}%")
                    ->take(10)
                    ->get(['product_id', 'product_name', 'product_unit', 'product_type', 'product_note']);
                $this->newItems[$index]['product_results'] = $results;
                $this->newItems[$index]['product_results_visible'] = $results->isNotEmpty();
            } else {
                $this->newItems[$index]['product_results'] = [];
                $this->newItems[$index]['product_results_visible'] = false;
            }
        }
    }

    public function selectProductForNewItem($index, $productId)
    {
        $product = \App\Models\products\ProductModel::find($productId);
        $this->newItems[$index]['product_id'] = $productId;
        $this->newItems[$index]['product_search'] = $product ? $product->product_name : '';
        $this->newItems[$index]['product_name'] = $product ? $product->product_name : '';
        $this->newItems[$index]['product_type'] = $product ? $product->product_type : '';
        $this->newItems[$index]['product_unit'] = $product ? $product->product_unit : '';
        $this->newItems[$index]['product_detail'] = $product ? ($product->product_size ?? $product->product_note ?? '') : '';
        $this->newItems[$index]['product_calculation'] = $product ? $product->product_calculation : null;
        $this->newItems[$index]['selected_from_dropdown'] = true;
        $this->newItems[$index]['product_results'] = [];
        $this->newItems[$index]['product_results_visible'] = false;
    }

    public function calculateTotals()
    {
        // 1) คำนวณยอดรวมก่อน VAT (ทั้งสินค้ารวมกัน)
        $subtotal = collect($this->order->items)->sum(function ($i) {
            $q = (float) ($i->quantity ?? 0);
            $up = (float) ($i->unit_price ?? 0);
            $len = max(1, (float) ($i->product_length ?? 1));
            $th = max(1, (float) ($i->product_calculation ?? 1));
            return $q * $up * $len * $th;
        });

        // 2) หักส่วนลด (ถ้ามี)
        $netSubtotal = max(0, $subtotal - ($this->order->order_discount ?? 0));

        // 3) ถ้าไม่เปิด VAT → เสร็จ
        if (!($this->order->order_enable_vat ?? false)) {
            $this->order_subtotal = $netSubtotal;
            $this->order_vat = 0;
            $this->order_grand_total = $netSubtotal;
            return;
        }

        // 4) คำนวณยอดที่นำมาคิด VAT (เฉพาะรายการที่ product_vat == true)
        $vatableBase = collect($this->order->items)
            ->filter(fn($i) => !empty($i->product_vat))
            ->sum(function ($i) {
                $q = (float) ($i->quantity ?? 0);
                $up = (float) ($i->unit_price ?? 0);
                $len = max(1, (float) ($i->product_length ?? 1));
                $th = max(1, (float) ($i->product_calculation ?? 1));
                return $q * $up * $len * $th;
            });

        // 5) ถ้า VAT รวมในราคา (VAT-In)
        if ($this->order->order_vat_included ?? false) {
            $netVatable = round($vatableBase / 1.07, 2);
            $vatAmount = round($vatableBase - $netVatable, 2);
            $this->order_subtotal = round($netSubtotal - $vatAmount, 2);
            $this->order_vat = $vatAmount;
            $this->order_grand_total = round($this->order_subtotal + $vatAmount, 2);
        } else {
            $vatAmount = round($vatableBase * 0.07, 2);
            $this->order_subtotal = $netSubtotal;
            $this->order_vat = $vatAmount;
            $this->order_grand_total = round($netSubtotal + $vatAmount, 2);
        }
    }

    public function saveNewItems()
    {
        foreach ($this->newItems as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['selected_from_dropdown'])) continue;
            $product = \App\Models\products\ProductModel::find($item['product_id']);
            \App\Models\Orders\OrderItemsModel::create([
                'order_id' => $this->order->id,
                'product_id' => $item['product_id'],
                'product_name' => $product ? $product->product_name : '',
                'product_type' => $product ? $product->product_type : '',
                'product_unit' => $product ? $product->product_unit : '',
                'product_detail' => $item['product_detail'] ?? ($product ? $product->product_size : ''),
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
                'product_vat' => $item['product_vat'] ?? false,
                'added_reason' => $item['added_reason'],
                'added_note' => $item['added_note'],
            ]);
        }
        $this->newItems = [];
        $this->order = $this->order->fresh(['items']);
        $this->calculateTotals(); // <-- เพิ่ม trigger คำนวณยอดรวม
        session()->flash('success', 'เพิ่มสินค้าใหม่เรียบร้อย');
    }

    public function deleteOrderItem($itemId)
    {
        $item = \App\Models\Orders\OrderItemsModel::find($itemId);
        if ($item && $item->order_id == $this->order->id) {
            $item->delete();
            // refresh order items
            $this->order = $this->order->fresh(['items']);
            $this->calculateTotals(); // <-- เพิ่ม trigger คำนวณยอดรวม
            // refresh deliveredQtyMap
            $this->deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
                ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
                ->where('order_items.order_id', $this->order->id)
                ->select('order_items.id as order_item_id', \DB::raw('SUM(order_delivery_items.quantity) as delivered'))
                ->groupBy('order_items.id')
                ->pluck('delivered', 'order_item_id')
                ->toArray();
            session()->flash('success', 'ลบรายการสินค้าเรียบร้อย');
        } else {
            session()->flash('error', 'ไม่พบรายการสินค้านี้');
        }
    }

    public function deleteDelivery($deliveryId)
    {
        $delivery = \App\Models\Orders\OrderDeliverysModel::find($deliveryId);
        if ($delivery && $delivery->order_id == $this->order->id) {
            // ลบรายการสินค้าที่เกี่ยวข้องกับรอบจัดส่งนี้ก่อน (ถ้ามี)
            \App\Models\Orders\OrderDeliveryItems::where('order_delivery_id', $deliveryId)->delete();
            $delivery->delete();
            // refresh order deliveries
            $this->order = $this->order->fresh(['deliveries.deliveryItems']);
            // refresh deliveredQtyMap
            $this->deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
                ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
                ->where('order_items.order_id', $this->order->id)
                ->select('order_items.id as order_item_id', \DB::raw('SUM(order_delivery_items.quantity) as delivered'))
                ->groupBy('order_items.id')
                ->pluck('delivered', 'order_item_id')
                ->toArray();
            session()->flash('success', 'ลบรอบจัดส่งเรียบร้อย');
        } else {
            session()->flash('error', 'ไม่พบรอบจัดส่งนี้');
        }
    }
}
