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
    
    // ส่วนของการแสดงประวัติการพิมพ์
    public $showPrintHistory = false;
    public $selectedDeliveryId = null;
    public $printHistory = [];

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
        
        // ตรวจสอบและอัพเดทสถานะการจัดส่งของออเดอร์
        $order->updateDeliveryStatus();
        
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
        
        // ตรวจสอบและอัพเดทสถานะการจัดส่งของออเดอร์
        $this->order->updateDeliveryStatus();
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

        $this->order->load('deliveries.deliveryItems');            // อัพเดทสถานะการจัดส่งของ order หลังจากที่เปลี่ยนสถานะการจัดส่ง
        $currentStatus = $this->order->updateDeliveryStatus();

        // แปลงสถานะเป็นข้อความภาษาไทยเพื่อแสดงในการแจ้งเตือน
        $statusText = $this->getOrderStatusText($currentStatus);

        $this->dispatchBrowserEvent('notify', [
            'type' => 'success',
            'message' => 'ยืนยันว่ารอบจัดส่ง #' . $delivery->order_delivery_number . ' จัดส่งเรียบร้อย (สถานะออเดอร์: ' . $statusText . ')',
        ]);

        // ถ้าออร์เดอร��หลักไม่มี order_items เหลือแล้ว สถานะจะเปลี่ยนเป็น completed (ทำไว้ใน createNewDelivery)
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
            'product_note' => '',
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
        $this->newItems[$index]['product_note'] = $product ? $product->product_note : '';
        $this->newItems[$index]['product_detail'] = ''; // ให้ user กรอกเอง
        $this->newItems[$index]['product_weight'] = $product ? $product->product_weight : 0;
        $this->newItems[$index]['product_results'] = [];
        $this->newItems[$index]['product_results_visible'] = false;
        $this->newItems[$index]['selected_from_dropdown'] = true;
        
        $this->calculateTotals();
    }

    /**
     * ล้างการเลือกสินค้าและให้เลือกใหม่
     */
    public function clearProductSelectionForNewItem($index)
    {
        if (isset($this->newItems[$index])) {
            $this->newItems[$index]['product_id'] = null;
            $this->newItems[$index]['product_search'] = '';
            $this->newItems[$index]['product_name'] = '';
            $this->newItems[$index]['product_type'] = '';
            $this->newItems[$index]['product_unit'] = '';
            $this->newItems[$index]['product_note'] = '';
            $this->newItems[$index]['product_detail'] = '';
            $this->newItems[$index]['product_weight'] = 0;
            $this->newItems[$index]['product_results'] = [];
            $this->newItems[$index]['product_results_visible'] = false;
            $this->newItems[$index]['selected_from_dropdown'] = false;
            
            $this->calculateTotals();
        }
    }

    /**
     * บันทึกรายการสินค้าใหม่ที่เพิ่มเข้ามา
     */
    public function saveNewItems()
    {
        try {
            DB::beginTransaction();
            
            $validItems = [];
            
            // ตรวจสอบและกรองรายการที่สมบูรณ์
            foreach ($this->newItems as $newItem) {
                if (!empty($newItem['product_id']) && !empty($newItem['quantity']) && !empty($newItem['unit_price'])) {
                    $validItems[] = $newItem;
                }
            }
            
            if (empty($validItems)) {
                $this->dispatch('notify', type: 'warning', message: 'กรุณาเพิ่มรายละเอียดสินค้าให้ครบถ้วน');
                return;
            }
            
            // บันทึกรายการใหม่
            foreach ($validItems as $newItem) {
                \App\Models\Orders\OrderItemsModel::create([
                    'order_id' => $this->order->id,
                    'product_id' => $newItem['product_id'],
                    'product_name' => $newItem['product_name'] ?? '',
                    'product_note' => $newItem['product_note'] ?? '',
                    'product_detail' => $newItem['product_detail'] ?? '',
                    'product_unit' => $newItem['product_unit'] ?? '',
                    'product_type' => $newItem['product_type'] ?? '',
                    'quantity' => $newItem['quantity'],
                    'unit_price' => $newItem['unit_price'],
                    'product_vat' => $newItem['product_vat'] ?? false,
                    'product_calculation' => $newItem['product_calculation'] ?? 1,
                    'product_length' => $newItem['product_length'] ?? 0,
                    'added_reason' => $newItem['added_reason'] ?? null,
                    'added_note' => $newItem['added_note'] ?? '',
                ]);
            }
            
            // อัปเดตเรคคอร์ดออเดอร์ด้วยข้อมูลยอดรวมใหม่
            $this->order->update([
                'order_subtotal' => $this->order_subtotal,
                'order_vat' => $this->order_vat,
                'order_grand_total' => $this->order_grand_total,
            ]);
            
            DB::commit();
            
            // รีโหลดข้อมูลและล้างฟอร์ม
            $this->order = $this->order->fresh(['items']);
            $this->newItems = [];
            $this->addRowMode = false;
            
            $this->dispatch('notify', type: 'success', message: 'เพิ่มรายการสินค้าใหม่เรียบร้อยแล้ว');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ลบแถวของรายการสินค้าใหม่
     */
    public function removeRow($index)
    {
        if (isset($this->newItems[$index])) {
            unset($this->newItems[$index]);
            // ทำให้ array index ติดต่อกัน
            $this->newItems = array_values($this->newItems);
            $this->calculateTotals();
        }
    }

    /**
     * ลบรายการสินค้าจาก Order
     */
    public function deleteOrderItem($itemId)
    {
        try {
            DB::beginTransaction();
            
            $orderItem = \App\Models\Orders\OrderItemsModel::find($itemId);
            
            if (!$orderItem) {
                $this->dispatch('notify', type: 'error', message: 'ไม่พบรายการสินค้าที่ต้องการลบ');
                return;
            }
            
            // ตรวจสอบว่ารายการนี้อยู่ในออร์เดอร์ปัจจุบันหรือไม่
            if ($orderItem->order_id !== $this->order->id) {
                $this->dispatch('notify', type: 'error', message: 'ไม่สามารถลบรายการของออร์เดอร์อื่นได้');
                return;
            }
            
            // ลบรายการสินค้า
            $orderItem->delete();
            
            DB::commit();
            
            // รีโหลดข้อมูลและคำนวณยอดรวมใหม่
            $this->order = $this->order->fresh(['items']);
            $this->calculateTotals();
            
            $this->dispatch('notify', type: 'success', message: 'ลบรายการสินค้าเรียบร้อยแล้ว');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
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
            // สูตรที่ถูกต้อง: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
            $qty = (float)($item->quantity ?? 0);
            $unit = (float)($item->unit_price ?? 0);
            $calc = (isset($item->product_calculation) && $item->product_calculation !== '' && $item->product_calculation !== null) ? (float)$item->product_calculation : 1;
            $len = (isset($item->product_length) && $item->product_length !== '' && $item->product_length !== null) ? (float)$item->product_length : 1;
            
            $itemTotal = $unit * $calc * $len * $qty;
            $subtotal += $itemTotal;
            
            if ($item->product_vat && $this->order_enable_vat && !$this->order_vat_included) {
                $vatAmount += $itemTotal * 0.07;
            }
        }
        
        // คำนวณจาก newItems ที่กำลังจะเพิ่ม
        foreach ($this->newItems as $newItem) {
            if (!empty($newItem['product_id']) && !empty($newItem['quantity']) && !empty($newItem['unit_price'])) {
                // สูตรที่ถูกต้อง: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
                $qty = (float)($newItem['quantity'] ?? 0);
                $unit = (float)($newItem['unit_price'] ?? 0);
                $calc = (isset($newItem['product_calculation']) && $newItem['product_calculation'] !== '' && $newItem['product_calculation'] !== null) ? (float)$newItem['product_calculation'] : 1;
                $len = (isset($newItem['product_length']) && $newItem['product_length'] !== '' && $newItem['product_length'] !== null) ? (float)$newItem['product_length'] : 1;
                
                $itemTotal = $unit * $calc * $len * $qty;
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
                $truckTypeKey = $truckType->name ?? 'unknown';
                if (!isset($summary['truck_types'][$truckTypeKey])) {
                    $summary['truck_types'][$truckTypeKey] = 0;
                }
                $summary['truck_types'][$truckTypeKey]++;
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

    /**
     * ลบรายการจัดส่ง
     */
    public function deleteDelivery($deliveryId)
    {
        try {
            DB::beginTransaction();
            
            // ค้นหา delivery ที่ต้องการลบ
            $delivery = OrderDeliverysModel::find($deliveryId);
            
            if (!$delivery) {
                $this->dispatch('notify', type: 'error', message: 'ไม่พบรายการจัดส่งที่ต้องการลบ');
                return;
            }
            
            // ตรวจสอบว่ารายการจัดส่งนี้อยู่ในออร์เดอร์ปัจจุบันหรือไม่
            if ($delivery->order_id !== $this->order->id) {
                $this->dispatch('notify', type: 'error', message: 'ไม่สามารถลบรายการจัดส่งของออร์เดอร์อื่นได้');
                return;
            }
            
            // ลบ delivery items ก่อน (foreign key constraint)
            $delivery->deliveryItems()->delete();
            
            // ลบ delivery
            $delivery->delete();
            
            DB::commit();
            
            // รีโหลดข้อมูล
            $this->order = $this->order->fresh(['deliveries.deliveryItems.orderItem']);
            
            // อัพเดทสถานะการจัดส่งของ order หลังจากลบรายการจัดส่ง
            $currentStatus = $this->order->updateDeliveryStatus();
            
            // แปลงสถานะเป็นข้อความภาษาไทยเพื่อแสดงในการแจ้งเตือน
            $statusText = $this->getOrderStatusText($currentStatus);
            
            $this->dispatch('notify', type: 'success', message: 'ลบรายการจัดส่งเรียบร้อยแล้ว (สถานะออเดอร์: ' . $statusText . ')');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * แสดงประวัติการพิมพ์ใบส่งของ
     * 
     * @param int $deliveryId รหัสใบส่งของ
     * @return void
     */
    public function showDeliveryPrintHistory(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        
        // ดึงข้อมูลประวัติการพิมพ์
        $printHistory = \App\Models\DeliveryPrint::where('order_delivery_id', $deliveryId)
                        ->orderBy('print_count', 'asc')
                        ->get()
                        ->toArray();
                        
        $this->printHistory = $printHistory;
        $this->showPrintHistory = true;
    }
    
    /**
     * ปิดหน้าต่างแสดงประวัติการพิมพ์
     * 
     * @return void
     */
    public function closePrintHistory(): void
    {
        $this->showPrintHistory = false;
        $this->selectedDeliveryId = null;
        $this->printHistory = [];
    }

    /**
     * ตรวจสอบและอัพเดทสถานะการจัดส่งของออเดอร์
     * 
     * @return string สถานะการจัดส่งปัจจุบัน
     */
    public function checkDeliveryStatus(): string
    {
        // เรียกใช้ฟังก์ชันอัพเดทสถานะการจัดส่งจาก OrderModel
        $status = $this->order->updateDeliveryStatus();
        
        // รีโหลดข้อมูลออเดอร์เพื่อให้มีข้อมูลล่าสุด
        $this->order = $this->order->fresh();
        
        // แปลงสถานะเป็นข้อความภาษาไทยเพื่อแสดงในการแจ้งเตือน
        $statusText = $this->getOrderStatusText($status);
        
        // แจ้งเตือนการอัพเดทสถานะ
        $this->dispatch('notify', 
            type: 'success', 
            message: 'อัปเดตสถานะการจัดส่งเป็น "' . $statusText . '"'
        );
        
        return $status;
    }

    /**
     * แปลงรหัสสถานะการจัดส่งเป็นข้อความภาษาไทย
     * 
     * @param string $status รหัสสถานะ
     * @return string ข้อความสถานะภาษาไทย
     */
    private function getOrderStatusText(string $status): string
    {
        return match($status) {
            'open' => 'เปิดรายการ', // ใช้แทน pending
            'processing' => 'กำลังดำเนินการ',
            'someproducts' => 'ส่งสินค้าบางส่วน',
            'completed' => 'ส่งสินค้าครบแล้ว',
            'delivered' => 'ส่งสินค้าครบแล้ว', // เพื่อความเข้ากันได้กับโค้ดเดิม
            'cancelled' => 'ยกเลิกแล้ว',
            // 'pending' ไม่ใช้แล้ว เนื่องจากไม่ได้อยู่ในค่าที่อนุญาตในฐานข้อมูล
            default => $status
        };
    }
}
