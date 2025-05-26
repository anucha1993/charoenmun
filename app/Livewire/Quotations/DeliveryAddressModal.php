<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use App\Models\customers\deliveryAddressModel;

class DeliveryAddressModal extends Component
{
    public ?int $delivery_id = null;
    public ?int $customer_id = null;

    public string $delivery_contact_name = '';
    public string $delivery_phone = '';
    public string $delivery_number = '';
    public string $delivery_province = '';
    public string $delivery_amphur = '';
    public string $delivery_district = '';
    public string $delivery_zipcode = '';

    public function mount($customer_id, $delivery_id = null)
    {
        $this->customer_id = $customer_id;

        if ($delivery_id) {
            $delivery = deliveryAddressModel::findOrFail($delivery_id);
            $this->delivery_id = $delivery->id;
            $this->delivery_contact_name = $delivery->delivery_contact_name;
            $this->delivery_phone = $delivery->delivery_phone;
            $this->delivery_number = $delivery->delivery_number;
            $this->delivery_province = $delivery->delivery_province;
            $this->delivery_amphur = $delivery->delivery_amphur;
            $this->delivery_district = $delivery->delivery_district;
            $this->delivery_zipcode = $delivery->delivery_zipcode;
        }
    }

    public function save()
    {
        $data = $this->validate([
            'delivery_contact_name' => 'required',
            'delivery_phone' => 'required',
            'delivery_number' => 'required',
            'delivery_province' => 'required',
            'delivery_amphur' => 'required',
            'delivery_district' => 'required',
            'delivery_zipcode' => 'required',
        ]);

        $data['customer_id'] = $this->customer_id;

        deliveryAddressModel::updateOrCreate(['id' => $this->delivery_id], $data);

        $this->dispatch('notify', ['message' => 'บันทึกที่อยู่เรียบร้อยแล้ว']);
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
