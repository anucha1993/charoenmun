<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\customers\customerModel;
use App\Models\customers\deliveryAddressModel;

class DeliveryAddressModal extends Component
{
    public ?int $delivery_id = null;
    public ?int $customer_id = null;
    public array $items = [];
    public string $delivery_customer_id = '';
    public string $customer_name = '';

    public array $deliveryForm = [
        'delivery_customer_id' => '',
        'delivery_contact_name' => '',
        'delivery_phone' => '',
        'delivery_address' => '',  // ใช้แค่ฟิลด์เดียวสำหรับที่อยู่เต็ม
    ];

    public bool $editing = false;
    public ?int $editing_delivery_id = null;

    #[On('edit-delivery-modal')]
    public function editDelivery($deliveryId)
    {
        $delivery = deliveryAddressModel::find($deliveryId);
        $customer = customerModel::find($delivery->customer_id);

        if ($delivery) {
            $this->editing = true;
            $this->editing_delivery_id = $deliveryId;
            $this->customer_id = $delivery->customer_id;
            $this->customer_name = $customer->customer_name;
            $this->deliveryForm = [
                'delivery_contact_name' => $delivery->delivery_contact_name,
                'delivery_phone' => $delivery->delivery_phone,
                'delivery_address' => $delivery->delivery_address ?? '', // ใช้ฟิลด์เดียว
            ];

            $this->dispatch('open-delivery-modal'); // เปิด modal
        }
    }

    public function updateDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'required|string|max:50',
            'deliveryForm.delivery_address' => 'required|string|max:1000',
        ]);

        if ($this->editing && $this->editing_delivery_id) {
            deliveryAddressModel::where('id', $this->editing_delivery_id)->update([
                'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
                'delivery_phone' => $this->deliveryForm['delivery_phone'],
                'delivery_address' => $this->deliveryForm['delivery_address'],
            ]);

            $this->dispatch('delivery-updated-success', [
                'customerId' => $this->customer_id,
                'deliveryId' => $this->editing_delivery_id,
            ]);

            $this->resetInput();
            $this->dispatch('close-delivery-modal');
        }
    }

    public function mount()
    {
        // ไม่ต้องโหลด provinces เพราะไม่ใช้แล้ว
    }

    #[On('open-delivery-modal')]
    public function openDeliveryModal($customerId = null)
    {
        if (!$customerId) {
            return;
        }

        $customer = customerModel::find($customerId);

        if ($customer) {
            $this->delivery_customer_id = $customerId;
            $this->customer_name = $customer->customer_name;
            $this->dispatch('open-delivery-modal'); // เปิด modal ผ่าน JS
        }
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'required|string|max:50',
            'deliveryForm.delivery_address' => 'required|string|max:1000',
        ]);

        $delivery = deliveryAddressModel::create([
            'customer_id' => $this->delivery_customer_id,
            'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
            'delivery_phone' => $this->deliveryForm['delivery_phone'],
            'delivery_address' => $this->deliveryForm['delivery_address'],
        ]);

        // แจ้ง parent ว่ามีการเพิ่มข้อมูลใหม่
        $this->dispatch('delivery-created-success', [
            'customerId' => $this->delivery_customer_id,
            'deliveryId' => $delivery->id,
        ]);
        $this->dispatch('delivery-created-success', ['deliveryId' => $delivery->id])->to(\App\Livewire\Orders\OrderDelivery::class);

        $this->resetInput();
        $this->dispatch('close-delivery-modal');
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->dispatch('close-delivery-modal');
    }

    public function resetInput()
    {
        $this->deliveryForm = [
            'delivery_contact_name' => '',
            'delivery_phone' => '',
            'delivery_address' => '',
        ];
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
