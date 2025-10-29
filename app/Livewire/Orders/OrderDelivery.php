<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\OrderDeliveryService;
use App\Models\customers\customerModel;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\Orders\OrderDeliverysModel;
use App\Models\customers\deliveryAddressModel;

#[Layout('layouts.horizontal')]
class OrderDelivery extends Component
{
    public ?int $order_id = null; // id ที่รับมาจาก route
    public ?int $delivery_id = null; // ถ้า view ต้องการ
    public ?OrderModel $orderModel = null; // เก็บ Order ตัวเดียว
    public Collection $orderItems;
    public ?customerModel $customer = null;
    public Collection $customerDelivery;
    public ?deliveryAddressModel $selectedDelivery = null;
    public ?int $selected_delivery_id = null;
    public ?int $delivery_address_id = null;
    public ?int $customer_id = null;
    public array $items = [];
    public array $stocks = []; // สต็อกเริ่มต้นจาก Order Items (คือ quantity ที่สั่งในใบสั่งซื้อหลัก)
    public array $stocksLeft = []; // สต็อกที่เหลือหลังหักการจัดส่งอื่น ๆ และที่กรอกในฟอร์มปัจจุบัน

    public ?OrderDeliverysModel $deliveryModel = null;
    public bool $editing = false;

    public float $order_delivery_subtotal = 0;
    public float $order_delivery_vat = 0;
    public float $order_delivery_discount = 0;
    public float $order_delivery_grand_total = 0;
    public float $netSubtotal = 0;
    public bool $order_delivery_enable_vat = false;
    public bool $order_delivery_vat_included = false;
    public string $order_delivery_date;
    public ?string $order_delivery_note = null;
    public ?string $order_delivery_status = 'pending';
    public ?int $order_delivery_status_order = 0;

    public function mount(OrderModel $order, ?OrderDeliverysModel $delivery)
    {
        $this->order_delivery_date = now()->format('Y-m-d');
        $this->orderModel = $order->load('items.product', 'customer');
        $this->order_id = $order->id;
        $this->orderItems = $this->orderModel->items;
        $this->customer_id = $this->orderModel->customer_id;
        $this->customerDelivery = $this->customer_id ? deliveryAddressModel::where('customer_id', $this->customer_id)->get() : collect();

        // Initialize base stocks from the order items (ใช้ order_item_id เป็น key)
        foreach ($this->orderItems as $oi) {
            $this->stocks[$oi->id] = $oi->quantity; // ใช้ order_item_id แทน product_id
        }

        if ($delivery && $delivery->id !== null) {
            // กำหนดโหมดแก้ไข
            $this->editing = true;
            $this->delivery_id = $delivery->id;
            $this->deliveryModel = $delivery->load('deliveryItems.orderItem');
            
            // ตั้งค่า delivery address
            $this->fillFromDeliveryModel($delivery);
            
            // ดึงข้อมูล items และค่าอื่นๆ
            $this->fillFromDelivery($delivery);
        } else {
            $this->editing = false;
            $this->order_delivery_note = '';
            
            // Create mode: สร้างรายการสินค้าทั้งหมดที่มี stock เหลือ
            $this->createAllAvailableItems();
        }

        $this->refreshStocksLeft();
        $this->recalculateTotals();
    }

    private function fillFromDelivery(OrderDeliverysModel $d): void
    {
        /* map deliveryItems -> $items */
        $this->items = $d->deliveryItems
            ->map(function (OrderDeliveryItems $di) {
                $oi = $di->orderItem;
                if (!$oi) {
                    // This should ideally not happen if data integrity is maintained
                    // \Log::warning("Order item not found for delivery item ID: {$di->id}");
                    return null; // Filter out invalid items
                }

                return [
                    'order_item_id' => $di->order_item_id, // ✅ เพิ่ม order_item_id เพื่อให้ dropdown selected ถูกต้อง
                    'product_id' => $oi->product_id,
                    'product_name' => $oi->product_name,
                    'product_type' => $oi->product_type,
                    'product_unit' => $oi->product_unit,
                    'product_detail' => $oi->product_detail,
                    'product_note' => $di->product_note ?? '', // Edit mode: ดึงจาก delivery_items
                    'product_length' => $oi->product_length,
                    'product_weight' => $oi->product_weight,
                    'product_calculation' => $di->product_calculation,
                    'quantity' => $di->quantity,
                    'unit_price' => $di->unit_price,
                    'total' => $di->total,
                ];
            })
            ->filter() // Remove null entries
            ->values()
            ->all();

        $this->order_delivery_date = $d->order_delivery_date->format('Y-m-d');
        $this->order_delivery_note = $d->order_delivery_note;
        $this->order_delivery_subtotal = (float) $d->order_delivery_subtotal;
        $this->order_delivery_discount = (float) $d->order_delivery_discount;
        $this->order_delivery_vat = (float) $d->order_delivery_vat;
        $this->order_delivery_enable_vat = (bool) $d->order_delivery_enable_vat;
        $this->order_delivery_grand_total = (float) $d->order_delivery_grand_total;
        $this->order_delivery_vat_included = (bool) $d->order_delivery_vat_included;
        $this->delivery_address_id = $d->delivery_address_id;
    }

    #[On('delivery-updated-success')]
    public function handleDeliveryUpdatedSuccess(array $payload): void
    {
        $id = $payload['deliveryId'] ?? null;

        $this->selected_delivery_id = $id;
        $this->selectedDelivery = $id ? deliveryAddressModel::find($id) : null;
    }

    public function updatedSelectedDeliveryId($id): void
    {
        $this->selectedDelivery = deliveryAddressModel::find($id);
    }

    #[On('delivery-created-success')]
    public function handleDeliveryCreatedSuccess(array $payload): void
    {
        $id = $payload['deliveryId'] ?? null;
        $this->selected_delivery_id = $id;
        $this->selectedDelivery = $id ? deliveryAddressModel::find($id) : null;

        $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();
    }

    public function openDeliveryModal(int $customer_id)
    {
        $this->dispatch('open-delivery-modal', $customer_id);
    }

    private function fillFromDeliveryModel(OrderDeliverysModel $delivery): void
    {
        $this->selected_delivery_id = $delivery->delivery_address_id;
        $this->selectedDelivery = deliveryAddressModel::find($delivery->delivery_address_id);
    }

    public function refreshCustomers(): void
    {
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();
    }

    public function addEmptyItem(): void
    {
        $this->items[] = [
            'order_item_id' => null,
            'product_id' => null,
            'product_name' => '',
            'product_type' => '',
            'product_unit' => '',
            'product_calculation' => 1,
            'product_length' => 1,
            'product_weight' => null,
            'product_detail' => null,
            'product_note' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
        $this->refreshStocksLeft();
    }

    private function createAllAvailableItems(): void
    {
        $this->items = []; // Clear existing items

        foreach ($this->orderItems as $oi) {
            $baseStock = $this->stocks[$oi->id] ?? 0; // ใช้ order_item_id
            $delivered = $this->getDeliveredByOthers($oi->id); // ส่ง order_item_id
            $availableStock = max(0, $baseStock - $delivered);
            
            // เพิ่มรายการเฉพาะสินค้าที่มี stock เหลือ
            if ($availableStock > 0) {
                $this->items[] = [
                    'order_item_id' => $oi->id, // เพิ่ม order_item_id
                    'product_id' => $oi->product_id,
                    'product_name' => $oi->product_name,
                    'product_type' => $oi->product_type,
                    'product_unit' => $oi->product_unit,
                    'product_calculation' => $oi->product_calculation ?? 1,
                    'product_length' => $oi->product_length ?? 1,
                    'product_weight' => $oi->product_weight,
                    'product_detail' => $oi->product_detail,
                    'product_note' => $oi->product_note ?? '',
                    'quantity' => $availableStock, // ใส่จำนวนที่เหลือทั้งหมด
                    'unit_price' => $oi->unit_price,
                    'total' => 0,
                ];
            }
        }

        // หากไม่มี stock เหลือเลย ให้เพิ่ม empty item
        if (empty($this->items)) {
            $this->addEmptyItem();
        }
    }

    public function updatedItems($value, $key): void
    {
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);
        $index = (int) $index;

        if ($field === 'order_item_id') {
            $orderItemId = (int) $value;

            if (!$orderItemId) {
                $this->items[$index] = [
                    'order_item_id' => null,
                    'product_id' => null,
                    'product_name' => '',
                    'product_type' => '',
                    'product_unit' => '',
                    'product_calculation' => 1,
                    'product_length' => 1,
                    'product_weight' => null,
                    'product_detail' => null,
                    'product_note' => '',
                    'quantity' => 1,
                    'unit_price' => 0,
                    'total' => 0,
                ];
                $this->recalculateTotals();
                $this->refreshStocksLeft();
                return;
            }

            foreach ($this->items as $i => $row) {
                if ($i === $index) {
                    continue;
                }

                if (($row['order_item_id'] ?? null) === $orderItemId) {
                    // ถ้าเลือก order item ที่มีอยู่แล้ว ให้แจ้งเตือนและไม่เปลี่ยนแปลง
                    $this->dispatch('error', message: 'รายการนี้มีในรายการแล้ว กรุณาแก้ไขจำนวนในรายการเดิม');
                    $this->items[$index]['order_item_id'] = null;
                    $this->items[$index]['product_id'] = null;
                    $this->recalculateTotals();
                    $this->refreshStocksLeft();
                    return;
                }
            }

            $oi = $this->orderItems->firstWhere('id', $orderItemId);
            if (!$oi) {
                $this->dispatch('error', message: 'ไม่พบรายการในออเดอร์');
                $this->items[$index]['order_item_id'] = null;
                $this->items[$index]['product_id'] = null;
                $this->recalculateTotals();
                $this->refreshStocksLeft();
                return;
            }

            $baseStock = $this->stocks[$orderItemId] ?? 0;
            $delivered = $this->getDeliveredByOthers($orderItemId);

            $usedInForm = collect($this->items)->filter(fn($r, $j) => $j !== $index && ($r['order_item_id'] ?? null) === $orderItemId)->sum('quantity');

            $maxAllowed = max(0, $baseStock - $delivered - $usedInForm);

            if ($maxAllowed <= 0) {
                $actualStockLeft = max(0, $baseStock - $delivered);
                $this->dispatch('qty-over', max: $actualStockLeft, name: $oi->product_name);
                $this->items[$index]['order_item_id'] = null;
                $this->items[$index]['product_id'] = null;
                $this->recalculateTotals();
                $this->refreshStocksLeft();
                return;
            }

            $this->items[$index] = [
                'order_item_id' => $orderItemId,
                'product_id' => $oi->product_id,
                'product_name' => $oi->product_name,
                'product_detail' => $oi->product_detail,
                'product_note' => $oi->product_note ?? '', // Create mode: ดึงจาก order_items
                'product_type' => $oi->product_type,
                'product_length' => $oi->product_length ?? 1,
                'product_calculation' => $oi->product_calculation ?? 1,
                'product_weight' => $oi->product_weight,
                'product_unit' => $oi->product_unit,
                'unit_price' => $oi->unit_price,
                'quantity' => $maxAllowed,
                'total' => 0,
            ];

            $this->recalculateTotals();
            $this->refreshStocksLeft();
            return;
        }

        if ($field === 'quantity') {
            $orderItemId = $this->items[$index]['order_item_id'] ?? null;
            if ($orderItemId) {
                $qty = (int) $value;

                $baseStock = $this->stocks[$orderItemId] ?? 0;
                $delivered = $this->getDeliveredByOthers($orderItemId);

                $usedInForm = collect($this->items)->filter(fn($r, $j) => $j !== $index && ($r['order_item_id'] ?? null) === $orderItemId)->sum('quantity');

                $maxAllowed = max(0, $baseStock - $delivered - $usedInForm);

                if ($qty > $maxAllowed) {
                    $this->items[$index]['quantity'] = $maxAllowed;
                    $actualStockLeft = max(0, $baseStock - $delivered);
                    $this->dispatch('qty-over', max: $actualStockLeft, name: $this->items[$index]['product_name']);
                } elseif ($qty < 1) {
                    $this->items[$index]['quantity'] = 1;
                }
            }
        }

        if (in_array($field, ['quantity', 'unit_price', 'product_calculation', 'product_length'])) {
            $qty = (float) ($this->items[$index]['quantity'] ?? 0);
            $up = (float) ($this->items[$index]['unit_price'] ?? 0);
            $calc = (float) ($this->items[$index]['product_calculation'] ?? 1);
            $len = (float) ($this->items[$index]['product_length'] ?? 1);

            $this->items[$index]['total'] = $qty * $up * max(1, $calc) * max(1, $len);
        }

        $this->recalculateTotals();
        $this->refreshStocksLeft();
    }

    private function getDeliveredByOthers(int $orderItemId): int
    {
        $query = OrderDeliveryItems::query()
            ->join('order_deliveries', 'order_delivery_items.order_delivery_id', '=', 'order_deliveries.id')
            ->where('order_deliveries.order_id', $this->order_id)
            ->where('order_delivery_items.order_item_id', $orderItemId);

        if ($this->editing && $this->deliveryModel) {
            $query->where('order_delivery_items.order_delivery_id', '!=', $this->deliveryModel->id);
        }

        return (int) $query->sum('order_delivery_items.quantity');
    }

    private function recalculateTotals(): void
    {
        foreach ($this->items as &$row) {
            $qty = (float) ($row['quantity'] ?? 0);
            $up = (float) ($row['unit_price'] ?? 0);
            $calc = (float) ($row['product_calculation'] ?? 1);
            $len = (float) ($row['product_length'] ?? 1);

            if ($len <= 0) {
                $len = 1;
            }
            if ($calc <= 0) {
                $calc = 1;
            }

            $row['total'] = $qty * $up * $calc * $len;
        }
        $this->calculateTotals();
    }

    private function refreshStocksLeft(): void
    {
        // STEP 1: เริ่มด้วย base stock (ปริมาณที่สั่งในใบสั่งซื้อหลัก)
        $left = $this->stocks;

        // STEP 2: หักยอดที่จัดส่งไปแล้วในบิลย่อยอื่น ๆ ของใบสั่งซื้อนี้
        // โดยใช้ helper function getDeliveredByOthers
        foreach ($left as $orderItemId => $qty) {
            $left[$orderItemId] = max(0, $qty - $this->getDeliveredByOthers($orderItemId));
        }

        // STEP 3: หักยอดที่กรอกในฟอร์มปัจจุบัน ($this->items)
        foreach ($this->items as $row) {
            $orderItemId = $row['order_item_id'] ?? null;
            if (!is_null($orderItemId) && isset($left[$orderItemId])) {
                $qtyInForm = (int) ($row['quantity'] ?? 0);
                $left[$orderItemId] = max(0, $left[$orderItemId] - $qtyInForm);
            } elseif (!is_null($orderItemId) && !isset($left[$orderItemId])) {
                // กรณี order_item_id มี แต่ไม่มีใน base stocks 
                $left[$orderItemId] = 0;
            }
        }

        // STEP 4: ตรวจสอบให้แน่ใจว่า order_item_id ทุกตัวใน orderItems มีใน stocksLeft ด้วย
        foreach ($this->orderItems as $oi) {
            if (!isset($left[$oi->id])) {
                $left[$oi->id] = 0;
            }
        }

        $this->stocksLeft = $left;
    }

    public function calculateTotals(): void
    {
        $this->order_delivery_subtotal = collect($this->items)->sum(function ($i) {
            $quantity = (float) ($i['quantity'] ?? 0);
            $unitPrice = (float) ($i['unit_price'] ?? 0);
            $length = (float) ($i['product_length'] ?? 1);
            $calculation = (float) ($i['product_calculation'] ?? 1);

            if ($length <= 0) {
                $length = 1;
            }
            if ($calculation <= 0) {
                $calculation = 1;
            }
            return $quantity * $unitPrice * $length * $calculation;
        });
        $netSubtotal = max(0, $this->order_delivery_subtotal - $this->order_delivery_discount);

        if (!$this->order_delivery_enable_vat) {
            $this->order_delivery_vat = 0;
            $this->order_delivery_grand_total = $netSubtotal;
            return;
        }
        if ($this->order_delivery_vat_included) {
            $net = round($netSubtotal / 1.07, 2);
            $this->order_delivery_vat = round($net * 0.07, 2);
            $this->order_delivery_subtotal = $net;
            $this->order_delivery_grand_total = $net + $this->order_delivery_vat;
        } else {
            $this->order_delivery_vat = round($netSubtotal * 0.07, 2);
            $this->order_delivery_grand_total = $netSubtotal + $this->order_delivery_vat;
        }
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
        $this->refreshStocksLeft();
        if (empty($this->items)) {
            $this->addEmptyItem();
        }
    }

    public function resetToAllItems(): void
    {
        if (!$this->editing) {
            $this->createAllAvailableItems();
            $this->calculateTotals();
            $this->refreshStocksLeft();
        }
    }

    public function updatedOrderDeliveryDiscount(): void
    {
        $this->calculateTotals();
    }
    public function updatedOrderDeliveryVatIncluded()
    {
        $this->calculateTotals();
    }
    public function updatedOrderDeliveryEnableVat()
    {
        $this->calculateTotals();
    }

    private function mapItemsForInsert(): array
    {
        return collect($this->items)
            ->filter(fn($row) => ($row['order_item_id'] ?? null) !== null && ($row['quantity'] ?? 0) > 0)
            ->map(function ($row) {
                $orderItem = $this->orderItems->firstWhere('id', $row['order_item_id']);

                return [
                    'order_item_id' => $orderItem->id ?? null,
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_type' => $row['product_type'],
                    'product_unit' => $row['product_unit'],
                    'product_detail' => $row['product_detail'],
                    'product_note' => $row['product_note'] ?? '', // บันทึกใน delivery_items
                    'product_length' => $row['product_length'],
                    'product_weight' => $row['product_weight'],
                    'product_calculation' => $row['product_calculation'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->filter(fn($item) => $item !== null && $item['order_item_id'] !== null)
            ->values()
            ->all();
    }

    public function saveDelivery()
    {
        $service = app(OrderDeliveryService::class);

        $payload = [
            'order_delivery_date' => $this->order_delivery_date,
            'delivery_address_id' => $this->selected_delivery_id,
            'order_delivery_status' => $this->order_delivery_status,
            'order_delivery_note' => $this->order_delivery_note,
            'order_delivery_subtotal' => $this->order_delivery_subtotal,
            'order_delivery_vat' => $this->order_delivery_vat,
            'order_delivery_discount' => $this->order_delivery_discount,
            'order_delivery_grand_total' => $this->order_delivery_grand_total,
            'order_delivery_enable_vat' => $this->order_delivery_enable_vat,
            'order_delivery_vat_included' => $this->order_delivery_vat_included,
            'order_delivery_status_order' => $this->order_delivery_status_order,
            'items' => $this->mapItemsForInsert(), // เตรียม array ของ items
        ];
        if ($this->editing) {
            $service->updateDelivery($this->deliveryModel, $payload);
            $msg = 'บันทึกการแก้ไขใบส่งสินค้าเรียบร้อย';
            $this->dispatch('notify', type: 'success', message: $msg);
        } else {
            $delivery = $service->storeDelivery($this->orderModel, $payload);
            $msg = 'สร้างใบส่งสินค้าเรียบร้อย เลขที่: ' . $delivery->order_delivery_number;
            $this->dispatch('notify', type: 'success', message: $msg);
        }
        return redirect()->route('orders.show', ['order' => $this->order_id]);
    }

    public function render()
    {
        return view('livewire.orders.order-delivery');
    }
}
