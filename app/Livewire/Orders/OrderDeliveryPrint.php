<?php

namespace App\Livewire\Orders;

use App\Models\Orders\OrderDeliverysModel;
use Livewire\Component;

class OrderDeliveryPrint extends Component
{
    public OrderDeliverysModel $delivery;

    public function mount(OrderDeliverysModel $delivery)
    {
        $delivery->load(['order','deliveryItems.orderItem']);
        $this->delivery = $delivery;
    }


    public function render()
    {
        return view('livewire.orders.order-delivery-print')->layout('layouts.horizontal-print', ['title' => 'Print Order Delivery']);;
    }
}
