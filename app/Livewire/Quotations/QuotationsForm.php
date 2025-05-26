<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\customers\CustomerModel;
use App\Models\customers\deliveryAddressModel;
use Livewire\Attributes\On;

class QuotationsForm extends Component
{
    public Collection $customers;
    public Collection $customerDelivery;

    public ?int $customer_id = null;
    public ?int $selected_delivery_id = null;

    public ?CustomerModel $selectedCustomer = null;
    public ?deliveryAddressModel $selectedDelivery = null;
    public array $customer = [];
    // protected $listeners = ['customer-saved' => 'refreshCustomerFromModal'];
    public ?int $selectedCustomerId = null;

    public function mount()
    {
        $this->customers = CustomerModel::all();
        $this->customerDelivery = collect();
    }

#[On('customer-created-success')]
public function handleCustomerCreatedSuccess(array $payload)
{
    $this->customer_id = (int) ($payload['customerId'] ?? 0);
    $this->refreshCustomers(); // ✅ reuse method ที่โหลดข้อมูลใหม่จริง
}

public function reloadCustomerListAndSelect($customerId)
{
    $this->customers = CustomerModel::all();
    $this->customer_id = (int) $customerId;
    $this->updatedCustomerId($this->customer_id);
}

    public function updatedCustomerId($value)
    {
        $this->selectedCustomer = CustomerModel::find($value);
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $value)->get();

        $this->selected_delivery_id = null;
        $this->selectedDelivery = null;
    }

    public function updatedSelectedDeliveryId($id)
    {
        $this->selectedDelivery = deliveryAddressModel::find($id);
    }

    public function setCustomerId($id)
    {
        $this->customer_id = $id;
        $this->updatedCustomerId($id); // เรียกใช้ logic เดิม
    }

   public function refreshCustomers()
{
    $this->customers = CustomerModel::all();

    if ($this->customer_id) {
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();
        $this->updatedCustomerId($this->customer_id);
    }

    $this->dispatch('$refresh'); // ✅ บอก Livewire render ใหม่
}

    public function render()
    {
        return view('livewire.quotations.quotations-form')->layout('layouts.horizontal', ['title' => 'Quotations']);
    }
}
