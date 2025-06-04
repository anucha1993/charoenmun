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

<<<<<<< HEAD
    public array $deliveryForm = [
        'delivery_customer_id' => '',
        'delivery_contact_name' => '',
        'delivery_phone' => '',
        'delivery_number' => '',
        'delivery_province' => '',
        'delivery_amphur' => '',
        'delivery_district' => '',
        'delivery_zipcode' => '',
    ];

    public array $deliveryProvinces = [],
        $deliveryAmphures = [],
        $deliveryDistricts = [];
    public bool $editing = false;
    public ?int $editing_delivery_id = null;

    #[On('edit-delivery-modal')]
    public function editDelivery($deliveryId)
    {
        $delivery = deliveryAddressModel::find($deliveryId);
        $customer = CustomerModel::find($delivery->customer_id);

        if ($delivery) {
            $this->editing = true;
            $this->editing_delivery_id = $deliveryId;
            $this->customer_id = $delivery->customer_id;
            $this->customer_name = $customer->customer_name;
            $this->deliveryForm = [
                'delivery_contact_name' => $delivery->delivery_contact_name,
                'delivery_phone' => $delivery->delivery_phone,
                'delivery_number' => $delivery->delivery_number,
                'delivery_province' => $delivery->delivery_province,
                'delivery_amphur' => $delivery->delivery_amphur,
                'delivery_district' => $delivery->delivery_district,
                'delivery_zipcode' => $delivery->delivery_zipcode,
            ];

            $this->deliveryAmphures = amphuresModel::where('province_code', $delivery->delivery_province)->pluck('amphur_name', 'amphur_code')->toArray();

            $this->deliveryDistricts = districtsModel::where('amphur_code', $delivery->delivery_amphur)->pluck('district_name', 'district_code')->toArray();

            $this->dispatch('open-delivery-modal'); // เปิด modal
        }
    }

    public function updateDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required',
            'deliveryForm.delivery_phone' => 'required',
            'deliveryForm.delivery_number' => 'required',
            'deliveryForm.delivery_province' => 'required',
            'deliveryForm.delivery_amphur' => 'required',
            'deliveryForm.delivery_district' => 'required',
            'deliveryForm.delivery_zipcode' => 'required',
        ]);

        if ($this->editing && $this->editing_delivery_id) {
            deliveryAddressModel::where('id', $this->editing_delivery_id)->update([
                'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
                'delivery_phone' => $this->deliveryForm['delivery_phone'],
                'delivery_number' => $this->deliveryForm['delivery_number'],
                'delivery_province' => $this->deliveryForm['delivery_province'],
                'delivery_amphur' => $this->deliveryForm['delivery_amphur'],
                'delivery_district' => $this->deliveryForm['delivery_district'],
                'delivery_zipcode' => $this->deliveryForm['delivery_zipcode'],
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
        $this->deliveryProvinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
    }
=======
    public array $provinces = [], $amphures = [], $districts = [];
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09

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

<<<<<<< HEAD
        // แจ้ง parent ว่ามีการเพิ่มข้อมูลใหม่
        $this->dispatch('delivery-created-success', [
            'customerId' => $this->delivery_customer_id,
            'deliveryId' => $delivery->id,
        ]);
        $this->dispatch('delivery-created-success',['deliveryId' => $delivery->id])->to(\App\Livewire\Orders\OrderDelivery::class);

        $this->resetInput();
=======
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09
        $this->dispatch('close-delivery-modal');
        $this->dispatch('delivery-saved', customerId: $this->customer_id);
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
