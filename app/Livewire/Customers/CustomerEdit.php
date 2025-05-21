<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\customers\CustomerModel;
use App\Models\customers\deliveryAddressModel;

class CustomerEdit extends Component
{
    public $customerId;

    // Fields สำหรับลูกค้า
    public string $customer_name = '';
    public string $customer_phone = '';

    // สำหรับที่อยู่จัดส่ง
    public array $deliveryAddresses = [];

    // Modal และการจัดการฟอร์มที่อยู่
    public bool $showDeliveryModal = false;
    public ?int $deliveryEditIndex = null;
    public array $deliveryForm = [];

    public function mount()
    {
        $customer = CustomerModel::with('deliveryAddresses')->findOrFail($this->customerId);

        // โหลดข้อมูลลูกค้า
        $this->customer_name = $customer->customer_name;
        $this->customer_phone = $customer->customer_phone;

        // โหลดที่อยู่จัดส่ง
        $this->deliveryAddresses = $customer->deliveryAddresses->toArray();
    }

    public function render()
    {
        return view('livewire.customers.customer-edit')->layout('layouts.horizontal', ['title' => 'Customers-Create']);;
    }

    public function openDeliveryModal($index = null)
    {
        $this->deliveryEditIndex = $index;

        if ($index !== null && isset($this->deliveryAddresses[$index])) {
            $this->deliveryForm = $this->deliveryAddresses[$index];
        } else {
            $this->deliveryForm = [
                'delivery_number' => '',
                'delivery_province' => '',
                'delivery_amphur' => '',
                'delivery_district' => '',
                'delivery_zipcode' => '',
                'delivery_contact_name' => '',
                'delivery_phone' => '',
            ];
        }

        $this->showDeliveryModal = true;
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_number' => 'required',
        ]);
    
        // ใช้ array_merge เพื่อความมั่นใจว่าทุก key เป็น string primitive
        $form = array_merge([
            'delivery_number' => '',
            'delivery_province' => '',
            'delivery_amphur' => '',
            'delivery_district' => '',
            'delivery_zipcode' => '',
            'delivery_contact_name' => '',
            'delivery_phone' => '',
        ], $this->deliveryForm);
    
        if ($this->deliveryEditIndex !== null && isset($this->deliveryAddresses[$this->deliveryEditIndex])) {
            // ✅ อัปเดต row เดิม
            $this->deliveryAddresses[$this->deliveryEditIndex] = $form;
        } else {
            // ✅ เพิ่มใหม่
            $this->deliveryAddresses[] = $form;
        }
    
        // รีเซต modal
        $this->reset(['deliveryForm', 'deliveryEditIndex', 'showDeliveryModal']);
    }
    

    public function removeDelivery($index)
    {
        unset($this->deliveryAddresses[$index]);
        $this->deliveryAddresses = array_values($this->deliveryAddresses);
    }

    public function update()
    {
        // อัปเดตข้อมูลลูกค้า
        $customer = CustomerModel::findOrFail($this->customerId);
        $customer->customer_name = $this->customer_name;
        $customer->customer_phone = $this->customer_phone;
        $customer->save();

        // ลบที่อยู่เดิมก่อน
        $customer->deliveryAddresses()->delete();

        // เพิ่มที่อยู่ใหม่
        foreach ($this->deliveryAddresses as $data) {
            $customer->deliveryAddresses()->create(array_merge($data, [
                'customer_id' => $customer->id
            ]));
        }

        session()->flash('success', 'บันทึกลูกค้าและที่อยู่จัดส่งเรียบร้อยแล้ว');
    }
}
