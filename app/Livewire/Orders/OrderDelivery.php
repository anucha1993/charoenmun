<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\OrderDeliveryService;
use App\Models\customers\CustomerModel;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\Orders\OrderDeliverysModel;
use App\Models\customers\deliveryAddressModel;

class OrderDelivery extends Component
{
    public ?int $order_id = null; // id ที่รับมาจาก route
    public ?int $delivery_id = null; // ถ้า view ต้องการ
    public ?OrderModel $orderModel = null; // เก็บ Order ตัวเดียว
    public Collection $orderItems;
    public ?CustomerModel $customer = null;
    public Collection $customerDelivery;
    public ?deliveryAddressModel $selectedDelivery = null;
    public ?int $selected_delivery_id = null;
    public ?int $customer_id = null;
    public array $items = [];
    public array $stocks = [];
    public array $stocksLeft = [];
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
    public ?string $payment_status = 'pending';
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

        if ($this->orderModel) {
            $this->fillFromModel($this->orderModel);
        }

        foreach ($this->orderItems as $oi) {
            $this->stocks[$oi->product_id] = $oi->quantity;
        }
        // dd($delivery->id);
        if ($delivery->id !== null) {
            $this->editing = true;
            $this->order_delivery_date = now()->format('Y-m-d');
            $this->deliveryModel = $delivery;
            $this->fillFromDelivery($delivery);
        } else {
            /* สร้างใหม่ */
            $this->editing = false;
            $this->order_delivery_date = now()->format('Y-m-d');
            $this->order_delivery_note = '';
            $this->addEmptyItem();
        }
        $this->refreshStocksLeft();
    }

    private function fillFromDelivery(OrderDeliverysModel $d): void
    {
        /* map deliveryItems -> $items */
        $this->items = $d->deliveryItems
            ->map(function (OrderDeliveryItems $di) {
                // $di->orderItem เป็นความสัมพันธ์ไปยัง OrderItemsModel
                $oi = $di->orderItem;

                return [
                    'product_id' => $oi->product_id,
                    'product_name' => $oi->product_name,
                    'product_type' => $oi->product_type,
                    'product_unit' => $oi->product_unit,
                    'product_detail' => $oi->product_detail,
                    'product_length' => $oi->product_length,
                    'product_weight' => $oi->product_weight,
                    // ค่า calculation ใช้ของ $di (อาจแตกต่างจาก order_item เมื่อผู้ใช้แก้ไข)
                    'product_calculation' => $di->product_calculation,
                    'quantity' => $di->quantity,
                    'unit_price' => $di->unit_price,
                    'total' => $di->total,
                ];
            })
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
        //  \Log::debug('fillFromDelivery: items = ', $this->items);
        $this->refreshStocksLeft();
    }

    // Update Address Delivery
    #[On('delivery-updated-success')]
    public function handleDeliveryUpdatedSuccess(array $payload): void
    {
        $id = $payload['deliveryId'] ?? null;

        $this->selected_delivery_id = $id;
        $this->selectedDelivery = $id ? deliveryAddressModel::find($id) : null;
    }
    // Select Address Delivery On Update
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

    // เปิด Modal address Delivery
    public function openDeliveryModal(int $customer_id)
    {
        $this->dispatch('open-delivery-modal', $customer_id);
    }

    private function fillFromModel(OrderModel $q): void
    {
        $this->selected_delivery_id = $q->delivery_address_id;
        $this->selectedDelivery = deliveryAddressModel::find($q->delivery_address_id);
    }

    public function refreshCustomers(): void
    {
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();

        // $this->dispatch('$refresh');   // ← ลบทิ้ง
    }

    public function addEmptyItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'product_name' => '',
            'product_type' => '',
            'product_unit' => '',
            'product_calculation' => 1,
            'product_length' => null,
            'product_weight' => null,
            'product_detail' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
        $this->refreshStocksLeft();
    }

    public function updatedItems($value, $key): void
    {
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

        if ($field === 'product_id') {
            $productId = (int) $value;
            if (!$productId) {
                return;
            }

            foreach ($this->items as $i => $row) {
                if ($i === $index) {
                    continue;
                }
                if ($row['product_id'] === $productId) {
                    $maxLeft = $this->stocksLeft[$productId] ?? 0;
                    $already = $row['quantity'] ?? 0;
                    $newQty = min($maxLeft, $already + ($this->items[$index]['quantity'] ?? 1));
                    if ($newQty === $already) {
                        $this->dispatch('qty-full', name: $row['product_name']);
                    }
                    $this->items[$i]['quantity'] = $newQty;

                    unset($this->items[$index]);
                    $this->items = array_values($this->items);

                    $this->recalculateTotals();
                    $this->refreshStocksLeft();
                    return;
                }
            }

            $oi = $this->orderItems->firstWhere('product_id', $productId);
            if (!$oi) {
                return;
            }

            $leftQty = $this->stocksLeft[$productId] ?? 0;
            if ($leftQty <= 0) {
                $this->dispatch('qty-over', max: 0, name: $oi->product_name);
                return;
            }

            $this->items[$index] = [
                'product_id' => $productId,
                'product_name' => $oi->product_name,
                'product_detail' => $oi->product_detail,
                'product_type' => $oi->product_type,
                'product_length' => $oi->product_length,
                'product_calculation' => $oi->product_calculation,
                'product_weight' => $oi->product_weight,
                'product_unit' => $oi->product_unit,
                'unit_price' => $oi->unit_price,
                'quantity' => $leftQty,
                'total' => 0,
            ];

            $this->recalculateTotals();
            $this->refreshStocksLeft();
            return;
        }

        if ($field === 'quantity') {
            $productId = $this->items[$index]['product_id'] ?? null;
            if ($productId) {
                $maxLeft = $this->stocksLeft[$productId] ?? 0;

                if ($this->editing && $this->deliveryModel) {
                    $originalQty = optional($this->deliveryModel->deliveryItems->firstWhere('orderItem.product_id', $productId))->quantity ?? 0;
                } else {
                    $originalQty = 0;
                }

                $used = collect($this->items)->filter(fn($item, $i) => $i != $index && $item['product_id'] === $productId)->sum('quantity');

                $allow = max(0, $maxLeft + $originalQty - $used);

                if ($value > $allow) {
                    $this->items[$index]['quantity'] = $allow;
                    $this->dispatch('qty-over', max: $allow, name: $this->items[$index]['product_name']);

                    // เรียก refresh เฉพาะตอนคืนค่าสินค้าเท่านั้น
                    $this->recalculateTotals();
                    $this->refreshStocksLeft();
                    return; // ต้อง return ทันทีเพื่อไม่เรียกซ้ำด้านล่าง
                }
            }
        }

        // กรณีเปลี่ยนเฉพาะราคา หรือรายละเอียดอื่น ไม่ต้องเรียก refreshStocksLeft()
        if (in_array($field, ['quantity', 'unit_price', 'product_calculation', 'product_length'])) {
            $qty = $this->items[$index]['quantity'] ?? 0;
            $up = $this->items[$index]['unit_price'] ?? 0;
            $calc = $this->items[$index]['product_calculation'] ?? 1;
            $len = $this->items[$index]['product_length'] ?? 1;
            $this->items[$index]['total'] = $qty * $up * $calc * $len;

            $this->recalculateTotals();
        }
    }

    private function recalculateTotals(): void
    {
        foreach ($this->items as &$row) {
            $row['total'] = $row['quantity'] * $row['unit_price'] * $row['product_calculation'] * $row['product_length'];
        }
        $this->calculateTotals();
    }

    private function refreshStocksLeft(): void
    {
        // STEP 1: เริ่มด้วย base stock ที่ mount() สร้างไว้
        $left = $this->stocks;

        // STEP 2: หักยอดที่จัดส่งไปแล้วในบิลย่อยอื่น ๆ
        $query = OrderDeliveryItems::query()->join('order_items', 'order_delivery_items.order_item_id', '=', 'order_items.id')->join('order_deliveries', 'order_delivery_items.order_delivery_id', '=', 'order_deliveries.id')->where('order_deliveries.order_id', $this->order_id);

        if ($this->editing && $this->deliveryModel) {
            // ในกรณี edit ให้ยกเว้นข้อมูลของบิลตัวเอง ไม่ต้องหักซ้ำ
            $query->where('order_delivery_items.order_delivery_id', '!=', $this->deliveryModel->id);
        }

        $deliveredMap = $query
            ->select(['order_items.product_id', DB::raw('SUM(order_delivery_items.quantity) as total_delivered')])
            ->groupBy('order_items.product_id')
            ->pluck('total_delivered', 'product_id')
            ->toArray();

        foreach ($deliveredMap as $pid => $usedQty) {
            if (isset($left[$pid])) {
                $left[$pid] = max(0, $left[$pid] - (int) $usedQty);
            }
        }

        // STEP 3: หักยอดที่กรอกในฟอร์มปัจจุบัน ($this->items) อีกที
        foreach ($this->items as $row) {
            $pid = $row['product_id'] ?? null;
            if (!is_null($pid) && isset($left[$pid])) {
                $qty = (int) ($row['quantity'] ?? 0);
                $left[$pid] = max(0, $left[$pid] - $qty);
            }
        }

        // STEP 4: เซ็ต 0 ถ้ามี product_id แต่ base stock ไม่มี
        foreach ($this->items as $row) {
            $pid = $row['product_id'] ?? null;
            if (!is_null($pid) && !isset($left[$pid])) {
                $left[$pid] = 0;
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
            $thickness = (float) ($i['product_calculation'] ?? 1);

            if ($length <= 0) {
                $length = 1;
            }
            if ($thickness <= 0) {
                $thickness = 1;
            }
            // return $quantity * $unitPrice * $length * $thickness;
            return $length * $thickness * $unitPrice * $quantity;
        });
        // ✅ ใช้ quote_subtotal ในการคำนวณ
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
            ->filter(fn($row) => $row['product_id']) // ตัดแถวว่าง
            ->map(function ($row) {
                $orderItemId = optional($this->orderItems->firstWhere('product_id', $row['product_id']))->id;

                return [
                    'order_item_id' => $orderItemId,
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_type' => $row['product_type'],
                    'product_unit' => $row['product_unit'],
                    'product_detail' => $row['product_detail'],
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
            ->values()
            ->all();
    }

    public function saveDelivery(): void
    {
        $service = app(OrderDeliveryService::class);

        $payload = [
            'order_delivery_date' => $this->order_delivery_date,
            'order_delivery_status' => $this->order_delivery_status,
            'payment_status' => $this->payment_status,
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
            $msg = 'อัปเดตใบจัดส่งเรียบร้อย';
        } else {
            $delivery = $service->storeDelivery($this->orderModel, $payload);
            $msg = 'สร้างใบจัดส่งเรียบร้อย เลขที่: ' . $delivery->order_delivery_number;
        }

        $this->dispatch('notify', type: 'success', message: 'สร้าง OrderDelivery เรียบร้อย เลขที่: ' . $delivery->order_delivery_number);
    }

    public function render()
    {
        return view('livewire.orders.order-delivery')->layout('layouts.horizontal', ['title' => 'DeliveryOrder']);
    }
}
