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
    public $order_subtotal_before_discount = 0;
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
        $this->order_enable_vat = true; // บังคับให้คิด VAT ทุกออเดอร์
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
        $this->calculateTotals();
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
        $this->newItems[$index]['product_length'] = $product ? ($product->product_length ?? 1) : 1;
        $this->newItems[$index]['product_calculation'] = $product ? ($product->product_calculation ?? 1) : 1;
        $this->newItems[$index]['selected_from_dropdown'] = true;
        $this->newItems[$index]['product_results'] = [];
        $this->newItems[$index]['product_results_visible'] = false;
        $this->calculateTotals();
    }

    public function removeRow($index)
    {
        unset($this->newItems[$index]);
        $this->newItems = array_values($this->newItems); // reindex the array
        $this->calculateTotals();
    }

    /**
     * ฟังก์ชันคำนวณยอดรวม, ส่วนลด, VAT, และยอดสุทธิ ตามมาตรฐานบัญชีไทย
     * - ยอดรวมก่อนหักส่วนลด: รวมราคาสินค้าทั้งหมด
     * - ส่วนลด: ใช้จาก order_discount
     * - ยอดสุทธิหลังหักส่วนลด: subtotal - discount
     * - VAT: 7% ของยอดสุทธิหลังหักส่วนลด (ถ้าเปิด VAT)
     * - จำนวนเงินทั้งสิ้น: ยอดสุทธิหลังหักส่วนลด + VAT
     */
    public function calculateTotals()
    {
        // 1. ยอดรวมก่อนหักส่วนลด
        $subtotal = 0;
        foreach ($this->order->items as $item) {
            $qty = (float)($item->quantity ?? 0);
            $unit = (float)($item->unit_price ?? 0);
            $calc = isset($item->product_calculation) && $item->product_calculation !== '' && $item->product_calculation !== null ? (float)$item->product_calculation : 1;
            $len = isset($item->product_length) && $item->product_length !== '' && $item->product_length !== null ? (float)$item->product_length : 1;
            // ใช้การคำนวณแบบเดิมสำหรับ existing items
            $factor = ($calc != 1) ? $calc : $len;
            if (!$factor || $factor <= 0) $factor = 1;
            $subtotal += $qty * $unit * $factor;
        }
        
        // รวมยอดจาก newItems
        foreach ($this->newItems as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $unit = (float)($item['unit_price'] ?? 0);
            $calc = (isset($item['product_calculation']) && $item['product_calculation'] !== '' && $item['product_calculation'] !== null) ? (float)$item['product_calculation'] : 1;
            $len = (isset($item['product_length']) && $item['product_length'] !== '' && $item['product_length'] !== null) ? (float)$item['product_length'] : 1;
            // ใช้สูตรใหม่สำหรับ newItems: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
            $subtotal += $unit * $calc * $len * $qty;
        }
        
        $this->order_subtotal_before_discount = round($subtotal, 2);

        // 2. ส่วนลด
        $discount = (float)($this->order_discount ?? 0);
        if ($discount > $subtotal) $discount = $subtotal;
        $this->order_discount = $discount;

        // 3. ยอดสุทธิหลังหักส่วนลด
        $net = max(0, $subtotal - $discount);
        $this->order_subtotal = round($net, 2);

        // 4. VAT (คิด 7% จากยอดสุทธิหลังหักส่วนลด ถ้าเปิด VAT)
        if (!empty($this->order_enable_vat)) {
            $vat = round($net * 0.07, 2);
        } else {
            $vat = 0;
        }
        $this->order_vat = $vat;

        // 5. จำนวนเงินทั้งสิ้น
        $this->order_grand_total = round($net + $vat, 2);
        
        // บันทึกยอดรวมลง database
        $this->saveOrderTotals();
    }

    public function save()
    {
        $this->validate([
            'order.order_number' => 'required|string|max:255',
            'order.customer_id' => 'required|exists:customers,id',
            'order.delivery_address_id' => 'required|exists:delivery_addresses,id',
            'order.order_date' => 'required|date',
            'order.due_date' => 'required|date',
            'order.status' => 'required|string',
            'order.items' => 'required|array',
            'order.items.*.product_id' => 'required|exists:products,product_id',
            'order.items.*.quantity' => 'required|numeric|min:1',
            'order.items.*.unit_price' => 'required|numeric|min:0',
            'order.items.*.total' => 'required|numeric|min:0',
        ]);

        $this->order->save();

        // Sync the order items
        $this->order->items()->sync(
            collect($this->order->items)->mapWithKeys(function ($item) {
                return [
                    $item['product_id'] => [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $item['total'],
                        'vat_amount' => $item['product_vat'] ? $item['total'] * 0.07 : 0,
                    ],
                ];
            })
        );

        $this->dispatch('notify', type: 'success', message: 'บันทึกออร์เดอร์เรียบร้อยแล้ว');
    }

    /**
     * เพิ่มสินค้าใหม่เข้าออเดอร์ (จาก newItems)
     */
    public function saveNewItems()
    {
        foreach ($this->newItems as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['unit_price'])) {
                continue;
            }
            
            // คำนวณ total ด้วยสูตรที่ถูกต้อง: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
            $qty = (float)($item['quantity'] ?? 0);
            $unit = (float)($item['unit_price'] ?? 0);
            $calc = (isset($item['product_calculation']) && $item['product_calculation'] !== '' && $item['product_calculation'] !== null) ? (float)$item['product_calculation'] : 1;
            $len = (isset($item['product_length']) && $item['product_length'] !== '' && $item['product_length'] !== null) ? (float)$item['product_length'] : 1;
            $total = $unit * $calc * $len * $qty;
            
            $this->order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'] ?? '',
                'product_type' => $item['product_type'] ?? '',
                'product_unit' => $item['product_unit'] ?? '',
                'product_detail' => $item['product_detail'] ?? '',
                'product_length' => $item['product_length'] ?? 1,
                'product_weight' => $item['product_weight'] ?? 0,
                'product_calculation' => $item['product_calculation'] ?? 1,
                'product_note' => $item['product_note'] ?? '',
                'product_vat' => $item['product_vat'] ?? false,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
                'added_reason' => $item['added_reason'] ?? null,
                'added_note' => $item['added_note'] ?? null,
            ]);
        }
        $this->newItems = [];
        $this->order->refresh();
        $this->calculateTotals();
        $this->dispatch('notify', type: 'success', message: 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว');
    }

    /**
     * ลบสินค้าออกจากออเดอร์
     */
    public function deleteOrderItem($itemId)
    {
        $item = $this->order->items()->find($itemId);
        if ($item) {
            $item->delete();
            $this->order->refresh();
            $this->calculateTotals();
            $this->dispatch('notify', type: 'success', message: 'ลบสินค้าเรียบร้อยแล้ว');
        } else {
            $this->dispatch('notify', type: 'error', message: 'ไม่พบรายการสินค้า');
        }
    }

    /**
     * บันทึกยอดรวมลง order model
     */
    public function saveOrderTotals()
    {
        $this->order->update([
            'order_subtotal' => $this->order_subtotal,
            'order_discount' => $this->order_discount,
            'order_vat' => $this->order_vat,
            'order_grand_total' => $this->order_grand_total,
        ]);
    }
}
