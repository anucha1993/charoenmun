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

    public function render()
    {
        return view('livewire.orders.order-delivery')->layout('layouts.horizontal', ['title' => 'DeliveryOrder']);
    }
}
