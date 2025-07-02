<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\Orders\OrderDeliverysModel;
use App\Enums\TruckType;

#[Layout('layouts.horizontal')]
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
        $this->order = $order->load(['customer', 'deliveryAddress', 'items', 'deliveries.deliveryItems.orderItem', 'payments']);
        // 🔽 สร้าง Map ของ order_item_id → จำนวนที่ถูกส่งไปแล้ว
        $this->deliveredQtyMap = \App\Models\Orders\OrderDeliveryItems::query()
            ->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.id as order_item_id', DB::raw('SUM(order_delivery_items.quantity) as delivered'))
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
        
        // อัปเดตน้ำหนักและแนะนำรถสำหรับทุก delivery
        foreach ($this->order->deliveries as $delivery) {
            $delivery->updateWeightAndTruckRecommendation();
        }
        
        // คำนวณยอดรวมล่าสุดจาก items ปัจจุบัน
        $this->calculateTotals();
    }
    public function render()
    {
        return view('livewire.orders.order-show', [
            'title' => 'Order #' . $this->order->order_number
        ]);
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
            'user_id' => Auth::id(),
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
        $this->newItems[$index]['product_weight'] = $product ? $product->product_weight : 0;
        $this->newItems[$index]['product_results'] = [];
        $this->newItems[$index]['product_results_visible'] = false;
        $this->newItems[$index]['selected_from_dropdown'] = true;
        
        $this->calculateTotals();
    }

    /**
     * คำนวณยอดรวมทั้งหมดของ Order
     */
    public function calculateTotals()
    {
        $subtotal = 0;
        $vatAmount = 0;
        
        // คำนวณจาก items ที่มีอยู่แล้ว
        foreach ($this->order->items as $item) {
            $itemTotal = $item->quantity * $item->unit_price;
            $subtotal += $itemTotal;
            
            if ($item->product_vat && $this->order_enable_vat && !$this->order_vat_included) {
                $vatAmount += $itemTotal * 0.07;
            }
        }
        
        // คำนวณจาก newItems ที่กำลังจะเพิ่ม
        foreach ($this->newItems as $newItem) {
            if (!empty($newItem['product_id']) && !empty($newItem['quantity']) && !empty($newItem['unit_price'])) {
                $itemTotal = $newItem['quantity'] * $newItem['unit_price'];
                $subtotal += $itemTotal;
                
                if (isset($newItem['product_vat']) && $newItem['product_vat'] && $this->order_enable_vat && !$this->order_vat_included) {
                    $vatAmount += $itemTotal * 0.07;
                }
            }
        }
        
        // อัปเดตค่าต่างๆ
        $this->order_subtotal_before_discount = $subtotal;
        $this->order_subtotal = $subtotal - $this->order_discount;
        $this->order_vat = $vatAmount;
        $this->order_grand_total = $this->order_subtotal + $this->order_vat;
    }

    /**
     * คำนวณน้ำหนักรวมของออเดอร์
     */
    public function calculateOrderWeight()
    {
        $totalWeight = 0;
        
        foreach ($this->order->items as $item) {
            $productWeight = $item->product->product_weight ?? 0;
            $totalWeight += $item->quantity * $productWeight;
        }
        
        return $totalWeight;
    }

    /**
     * แนะนำประเภทรถสำหรับออเดอร์
     */
    public function recommendTruckForOrder()
    {
        $totalWeight = $this->calculateOrderWeight();
        return TruckType::getRecommendedTruck($totalWeight);
    }

    /**
     * ดึงข้อมูลสรุปน้ำหนักและรถสำหรับการจัดส่ง
     */
    public function getDeliveryWeightSummary()
    {
        $summary = [
            'total_deliveries' => $this->order->deliveries->count(),
            'total_weight' => 0,
            'completed_weight' => 0,
            'pending_weight' => 0,
            'truck_types' => [],
        ];

        foreach ($this->order->deliveries as $delivery) {
            $weight = $delivery->total_weight_kg ?? 0;
            $summary['total_weight'] += $weight;

            if ($delivery->delivery_status === 'delivered') {
                $summary['completed_weight'] += $weight;
            } else {
                $summary['pending_weight'] += $weight;
            }

            if ($delivery->selected_truck_type) {
                $truckType = $delivery->selected_truck_type;
                if (!isset($summary['truck_types'][$truckType])) {
                    $summary['truck_types'][$truckType] = 0;
                }
                $summary['truck_types'][$truckType]++;
            }
        }

        return $summary;
    }

    /**
     * ดึงข้อมูลสรุปการขนส่งทั้งหมดของ Order
     */
    public function getOrderTransportSummary()
    {
        $totalOrderWeight = $this->calculateOrderWeight();
        $deliveries = $this->order->deliveries;
        
        $totalDeliveryWeight = 0;
        $truckTypes = [];
        $overweightCount = 0;
        $totalTrips = 0;
        
        foreach ($deliveries as $delivery) {
            $totalDeliveryWeight += $delivery->total_weight_kg ?? 0;
            
            if ($delivery->selected_truck_type) {
                $truckTypes[] = $delivery->selected_truck_type->label();
            }
            
            if ($delivery->isOverweight()) {
                $overweightCount++;
            }
            
            $totalTrips += $delivery->calculateRequiredTrips();
        }
        
        return [
            'total_order_weight_kg' => $totalOrderWeight,
            'total_order_weight_ton' => round($totalOrderWeight / 1000, 2),
            'total_delivery_weight_kg' => $totalDeliveryWeight,
            'total_delivery_weight_ton' => round($totalDeliveryWeight / 1000, 2),
            'deliveries_count' => $deliveries->count(),
            'truck_types' => array_unique($truckTypes),
            'overweight_deliveries' => $overweightCount,
            'total_trips_required' => $totalTrips,
            'recommended_truck_for_full_order' => TruckType::getRecommendedTruck($totalOrderWeight),
        ];
    }
}
