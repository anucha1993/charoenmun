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

    // public function updatedItems($value, $key): void
    // {
    //     [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

    //     /* เมื่อเลือกสินค้า */
    //     if ($field === 'product_id') {
    //         // หา order_item ของสินค้านั้น
    //         $oi = $this->orderItems->firstWhere('product_id', $value);
    //         if (!$oi) {
    //             return;
    //         }
    //         // ─── ถ้าแถวนี้มี id เก่า (คือแถวแก้ไขจาก DB) ให้ bypass merge
    //         $currentId = $this->items[$index]['id'] ?? null;

    //         // เติมรายละเอียดต่าง ๆ
    //         $this->items[$index]['product_name'] = $oi->product->product_name;
    //         $this->items[$index]['product_detail'] = $oi->product->product_size;
    //         $this->items[$index]['product_type'] = $oi->product->productType->value;
    //         $this->items[$index]['product_length'] = $oi->product->product_length;
    //         $this->items[$index]['product_weight'] = $oi->product->product_weight;
    //         $this->items[$index]['product_unit'] = $oi->product->productUnit->value;
    //         $this->items[$index]['unit_price'] = $oi->product->product_price;

    //         // ⭐ ใส่จำนวนคงเหลือจาก order_item
    //         $this->items[$index]['quantity'] = $oi->quantity;
    //     }

    //     /* เมื่อแก้จำนวนให้ตรวจว่าเกินสต็อกหรือไม่ */
    //     if ($field === 'quantity') {
    //         $productId = $this->items[$index]['product_id'] ?? null;
    //         if ($productId) {
    //             $max = $this->stocks[$productId] ?? 0;
    //             if ($value > $max) {
    //                 // ปรับกลับเป็นคงเหลือสูงสุด
    //                 $this->items[$index]['quantity'] = $max;
    //                 // แจ้งเตือนฝั่งเบราว์เซอร์ (รูปแบบใหม่ v3)
    //                 $this->dispatch('qty-over', max: $max, name: $this->items[$index]['product_name']);
    //             }
    //         }
    //     }

    //     /* คำนวณยอดรวมใหม่ทุกครั้ง */
    //     foreach ($this->items as &$line) {
    //         $line['total'] = $line['quantity'] * $line['unit_price'];
    //     }
    // }

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
                    /* จำนวนใหม่หลังรวม */
                    $max = $this->stocks[$productId] ?? 0;
                    $new = $row['quantity'] + ($this->items[$index]['quantity'] ?? 1);
                    /* ถ้าเกินสต็อกให้ตัดเหลือ max และเตือน */
                    if ($new > $max) {
                        $new = $max;
                        $this->dispatch('qty-over', max: $max, name: $this->items[$i]['product_name']);
                    }
                    $this->items[$i]['quantity'] = $new;
                    unset($this->items[$index]);
                    $this->items = array_values($this->items);
                    $this->recalculateTotals();
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
                if ($value > $max) {
                    $this->items[$index]['quantity'] = $max;
                    $this->dispatch('qty-over', max: $max, name: $this->items[$index]['product_name']);
                }
            }
        }
        if (in_array($field, ['quantity', 'unit_price'])) {
            $this->items[$index]['total'] = $this->items[$index]['quantity'] * $this->items[$index]['unit_price'];
        }
        $this->recalculateTotals(); // รวมทุกแถวอีกครั้ง
    }

    private function recalculateTotals(): void
    {
        foreach ($this->items as &$row) {
            $row['total'] = $row['quantity'] * $row['unit_price'];
        }
    }

    

    public function render()
    {
        return view('livewire.orders.order-delivery')->layout('layouts.horizontal', ['title' => 'DeliveryOrder']);
    }
}
