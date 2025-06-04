<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\products\ProductModel;
use App\Models\Orders\OrderItemsModel;
use App\Models\customers\CustomerModel;
use App\Models\Orders\OrderDeliveryItems;
use App\Models\Quotations\QuotationModel;
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

    public float $order_delivery_subtotal = 0;
    public float $order_delivery_vat = 0;
    public float $order_delivery_discount = 0;
    public float $order_delivery_grand_total = 0;
    public float $netSubtotal = 0;
    public bool $order_delivery_enable_vat = false;
    public bool $order_delivery_vat_included = false;


    public function mount(OrderModel $order): void
    {
        
        $this->orderModel = $order->load('items', 'customer');
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
        $this->stocksLeft = $this->stocks;
        $this->addEmptyItem();
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
    }

    public function updatedItems($value, $key): void
    {
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

        /* ─────────── 1) เมื่อเลือกสินค้า ─────────── */
        if ($field === 'product_id') {
            $productId = (int) $value;
            if (!$productId) {
                return;
            }

            /* loop หาแถวซ้ำ */
            foreach ($this->items as $i => $row) {
                if ($i === $index) {
                    continue;
                }
                if ($row['product_id'] === $productId) {
                    $max   = $this->stocks[$productId] ?? 0;
                    $new   = min($max, $row['quantity'] + ($this->items[$index]['quantity'] ?? 1));
                
                    if ($new == $row['quantity']) {            // เต็มสต๊อกแล้ว
                        $this->dispatch('qty-full', name: $row['product_name']);
                    }
                
                    $this->items[$i]['quantity'] = $new;
                    unset($this->items[$index]);                // ลบแถวปัจจุบัน
                    $this->items = array_values($this->items);  // re-index
                    $this->recalculateTotals();
                    $this->refreshStocksLeft();
                    return;
                }
            }
            /* ไม่ซ้ำ → เติมรายละเอียด + quantity ตามคงเหลือ */
            $oi = $this->orderItems->firstWhere('product_id', $productId);
            if (!$oi) {
                return;
            }
            $p = $oi->product;
            $this->items[$index] = [
                'product_id' => $productId,
                'product_name' => $p->product_name,
                'product_detail' => $p->product_size,
                'product_type' => $p->productType->value,
                'product_length' => $p->product_length,
                'product_calculation' => $p->product_calculation,
                'product_weight' => $p->product_weight,
                'product_unit' => $p->productUnit->value,
                'unit_price' => $p->product_price,
                'quantity' => $oi->quantity, // คงเหลือของ order_item
                'total' => 0,
            ];
        }
        if ($field === 'quantity') {
            $productId = $this->items[$index]['product_id'] ?? null;
            if ($productId) {
                $max = $this->stocks[$productId] ?? 0;
                /* หักจำนวนของแถวอื่น ๆ ออกก่อนบวกตัวเอง */
                $used = collect($this->items)->where('product_id', $productId)
                                             ->sum('quantity') - $value;
                $allow = max(0, $max - $used);
        
                if ($value > $allow) {
                    $this->items[$index]['quantity'] = $allow;
                    $this->dispatch('qty-over',
                        max: $allow,
                        name: $this->items[$index]['product_name']
                    );
                }
            }
        }
        if (in_array($field, ['quantity', 'unit_price'])) {
            $this->items[$index]['total'] = $this->items[$index]['quantity'] * $this->items[$index]['unit_price'];
        }
        $this->recalculateTotals(); // รวมทุกแถวอีกครั้ง
       
        $this->refreshStocksLeft();
     
    }

    private function recalculateTotals(): void
    {
        foreach ($this->items as &$row) {
            $row['total'] = $row['quantity'] * $row['unit_price'];
        }
        $this->calculateTotals();
       
    }

    private function refreshStocksLeft(): void
    {
        // เริ่มจาก stock ดิบ
        $left = $this->stocks;

        // หักจำนวนที่ถูกเลือกในทุกแถว (ที่ยังไม่ blank)
        foreach ($this->items as $row) {
            if ($row['product_id']) {
                $left[$row['product_id']] -= $row['quantity'];
            }
        }

        // ห้ามติดลบ
        foreach ($left as &$v) {
            $v = max(0, $v);
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
            return ($length * $thickness)* $unitPrice * $quantity ;
        });

        // ✅ ใช้ quote_subtotal ในการคำนวณ
        $netSubtotal = max(0, $this->order_delivery_subtotal - $this->order_delivery_discount);

        if (!$this->order_delivery_enable_vat) {
            $this->order_delivery_vat = 0;
            $this->order_delivery_grand_total = $netSubtotal;
            return;
        }

        if ($this->quote_vat_included) {
            $net = round($netSubtotal / 1.07, 2);
            $this->order_delivery_vat = round($net * 0.07, 2);
            $this->order_delivery_subtotal = $net;
            $this->order_delivery_grand_total = $net + $this->quote_vat;
        } else {
            $this->order_delivery_vat = round($netSubtotal * 0.07, 2);
            $this->order_delivery_grand_total = $netSubtotal + $this->order_deliverie_vat;
        }
    }

    public function render()
    {
        return view('livewire.orders.order-delivery')->layout('layouts.horizontal', ['title' => 'DeliveryOrder']);
    }
}
