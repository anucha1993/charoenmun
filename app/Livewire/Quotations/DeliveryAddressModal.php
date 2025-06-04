<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\customers\CustomerModel;
use App\Models\customers\deliveryAddressModel;
use App\Models\addressList\provincesModel;
use App\Models\addressList\amphuresModel;
use App\Models\addressList\districtsModel;

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

    #[On('open-delivery-modal')]
    public function openDeliveryModal($customerId = null)
    {
        if (!$customerId) {
            return;
        }

        $customer = CustomerModel::find($customerId);

        if ($customer) {
            $this->delivery_customer_id = $customerId;
            $this->customer_name = $customer->customer_name;
            $this->dispatch('open-delivery-modal'); // เปิด modal ผ่าน JS
        }
    }

    public function updatedDeliveryFormDeliveryProvince()
    {
        $province = $this->deliveryForm['delivery_province'] ?? '';
        $this->deliveryAmphures = amphuresModel::where('province_code', $province)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        $this->deliveryForm['delivery_amphur'] = '';
        $this->deliveryForm['delivery_district'] = '';
        $this->deliveryDistricts = [];
    }

    public function updatedDeliveryFormDeliveryAmphur()
    {
        $amphur = $this->deliveryForm['delivery_amphur'] ?? '';
        $this->deliveryDistricts = districtsModel::where('amphur_code', $amphur)->orderBy('district_name')->pluck('district_name', 'district_code')->toArray();
        $this->deliveryForm['delivery_district'] = '';
    }

    public function updatedDeliveryFormDeliveryDistrict()
    {
        $districtCode = $this->deliveryForm['delivery_district'] ?? '';
        $zipcode = districtsModel::where('district_code', $districtCode)->value('zipcode');
        $this->deliveryForm['delivery_zipcode'] = $zipcode ?? '';
    }

    public function updatedDeliveryFormDeliveryZipcode($zip)
    {
        if (strlen($zip) !== 5) {
            return;
        }

        $districts = districtsModel::where('zipcode', $zip)->get();
        if ($districts->isEmpty()) {
            return;
        }

        // ✅ Step 1: set จังหวัด
        $provinceCode = $districts->first()->province_code;
        $this->deliveryForm['delivery_province'] = $provinceCode;

        // ✅ Step 2: หาอำเภอจาก district (ไม่โหลดทั้งหมด)
        $amphurCodes = $districts->pluck('amphur_code')->unique();

        // ✅ โหลดเฉพาะอำเภอที่เกี่ยวข้องกับ zip นี้
        $this->deliveryAmphures = amphuresModel::whereIn('amphur_code', $amphurCodes)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        if ($amphurCodes->count() === 1) {
            $this->deliveryForm['delivery_amphur'] = $amphurCodes->first();

            // ✅ โหลดตำบลของอำเภอนั้นเท่านั้น
            $this->deliveryDistricts = districtsModel::where('amphur_code', $this->deliveryForm['delivery_amphur'])->orderBy('district_name')->pluck('district_name', 'district_code')->toArray();

            $districtsInAmphur = $districts->where('amphur_code', $this->deliveryForm['delivery_amphur']);
            if ($districtsInAmphur->count() === 1) {
                $this->deliveryForm['delivery_district'] = $districtsInAmphur->first()->district_code;
            }
        } else {
            $this->deliveryForm['delivery_amphur'] = '';
            $this->deliveryForm['delivery_district'] = '';
            $this->deliveryDistricts = [];
        }
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'required|string|max:50',
            'deliveryForm.delivery_number' => 'required|string|max:255',
            'deliveryForm.delivery_province' => 'required|string',
            'deliveryForm.delivery_amphur' => 'required|string',
            'deliveryForm.delivery_district' => 'required|string',
            'deliveryForm.delivery_zipcode' => 'required|string|size:5',
        ]);

        $delivery = deliveryAddressModel::create([
            'customer_id' => $this->delivery_customer_id,
            'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
            'delivery_phone' => $this->deliveryForm['delivery_phone'],
            'delivery_number' => $this->deliveryForm['delivery_number'],
            'delivery_province' => $this->deliveryForm['delivery_province'],
            'delivery_amphur' => $this->deliveryForm['delivery_amphur'],
            'delivery_district' => $this->deliveryForm['delivery_district'],
            'delivery_zipcode' => $this->deliveryForm['delivery_zipcode'],
        ]);

        // แจ้ง parent ว่ามีการเพิ่มข้อมูลใหม่
        $this->dispatch('delivery-created-success', [
            'customerId' => $this->delivery_customer_id,
            'deliveryId' => $delivery->id,
        ]);
        $this->dispatch('delivery-created-success',['deliveryId' => $delivery->id])->to(\App\Livewire\Orders\OrderDelivery::class);

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
            'delivery_number' => '',
            'delivery_province' => '',
            'delivery_amphur' => '',
            'delivery_district' => '',
            'delivery_zipcode' => '',
        ];
        $this->deliveryAmphures = [];
        $this->deliveryDistricts = [];
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
