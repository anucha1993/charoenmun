<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use App\Models\customers\deliveryAddressModel;
use App\Models\addressList\provincesModel;
use App\Models\addressList\amphuresModel;
use App\Models\addressList\districtsModel;
use Livewire\Attributes\On;

class DeliveryAddressModal extends Component
{
    public ?int $customer_id = null;

    public string $delivery_contact_name = '',
        $delivery_phone = '',
        $delivery_number = '',
        $delivery_province = '',
        $delivery_amphur = '',
        $delivery_district = '',
        $delivery_zipcode = '';

    public array $provinces = [], $amphures = [], $districts = [];

    #[On('open-delivery-modal')]
    public function open(array $params)
    {
        $this->reset();
        $this->customer_id = $params['customerId'] ?? null;
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        $this->dispatch('open-delivery-modal');
    }

    public function updatedDeliveryProvince()
    {
        $this->amphures = amphuresModel::where('province_code', $this->delivery_province)
            ->pluck('amphur_name', 'amphur_code')
            ->toArray();

        $this->delivery_amphur = '';
        $this->delivery_district = '';
        $this->districts = [];
    }

    public function updatedDeliveryAmphur()
    {
        $this->districts = districtsModel::where('amphur_code', $this->delivery_amphur)
            ->pluck('district_name', 'district_code')
            ->toArray();

        $this->delivery_district = '';
    }

    public function updatedDeliveryDistrict()
    {
        $this->delivery_zipcode = districtsModel::where('district_code', $this->delivery_district)->value('zipcode') ?? '';
    }

    public function save()
    {
        $this->validate([
            'delivery_contact_name' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'delivery_number' => 'required|string|max:255',
        ]);

        deliveryAddressModel::create([
            'customer_id' => $this->customer_id,
            'delivery_contact_name' => $this->delivery_contact_name,
            'delivery_phone' => $this->delivery_phone,
            'delivery_number' => $this->delivery_number,
            'delivery_province' => $this->delivery_province,
            'delivery_amphur' => $this->delivery_amphur,
            'delivery_district' => $this->delivery_district,
            'delivery_zipcode' => $this->delivery_zipcode,
        ]);

        $this->dispatch('close-delivery-modal');
        $this->dispatch('delivery-saved', customerId: $this->customer_id);
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
