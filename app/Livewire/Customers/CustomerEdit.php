<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\customers\CustomerModel;
use App\Models\addressList\amphuresModel;
use App\Models\globalsets\GlobalSetModel;
use App\Models\addressList\districtsModel;
use App\Models\addressList\provincesModel;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Support\Collection;

class CustomerEdit extends Component
{
    public $customerId;

    // Fields สำหรับลูกค้า
    public string $customer_code = '',
        $customer_name = '',
        $customer_type = '',
        $customer_level = '',
        $customer_taxid = '',
        $customer_contract_name = '',
        $customer_phone = '',
        $customer_email = '',
        $customer_idline = '',
        $customer_address = '',
        $customer_province = '',
        $customer_amphur = '',
        $customer_district = '',
        $customer_zipcode = '';

    public array $deliveryAddresses = [];

    // Modal และการจัดการฟอร์มที่อยู่
    public bool $showDeliveryModal = false;
    public ?int $deliveryEditIndex = null;
    public array $deliveryForm = [];

    //Select
    public Collection $customerType;
    public Collection $customerLevel;
    public array $provinces = [],
        $amphures = [],
        $districts = [];
    public array $deliveryProvinces = [],
        $deliveryAmphures = [],
        $deliveryDistricts = [];
    protected $listeners = ['removeDelivery'];
    public float $customer_pocket_money = 0;
    public string $confirm_password = '';

    public function mount()
    {
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        $this->deliveryProvinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();

        $set = GlobalSetModel::with('values')->find(1); // ประเภทลูกค้า
        $setLevel = GlobalSetModel::with('values')->find(2); // ระดับลูกค้า
        $this->customerType = $set?->values->where('status', 'Enable')->values() ?? collect();
        $this->customerLevel = $setLevel?->values->where('status', 'Enable')->values() ?? collect();

        $customer = CustomerModel::with('deliveryAddresses')->findOrFail($this->customerId);
        $this->customer_pocket_money = $customer->customer_pocket_money ?? 0;

        $this->customer_code = $customer->customer_code;
        $this->customer_name = $customer->customer_name;
        $this->customer_type = $customer->customer_type;
        $this->customer_level = $customer->customer_level;
        $this->customer_taxid = $customer->customer_taxid;
        $this->customer_contract_name = $customer->customer_contract_name;
        $this->customer_phone = $customer->customer_phone;
        $this->customer_email = $customer->customer_email;
        $this->customer_idline = $customer->customer_idline;
        $this->customer_address = $customer->customer_address;
        $this->customer_province = $customer->customer_province;

        $this->updatedCustomerProvince();
        $this->customer_amphur = $customer->customer_amphur;
        $this->updatedCustomerAmphur();
        $this->customer_district = $customer->customer_district;
        $this->updatedCustomerDistrict();
        $this->customer_zipcode = $customer->customer_zipcode;

        // โหลดที่อยู่จัดส่

        $this->deliveryAddresses = $customer->deliveryAddresses->toArray();
    }

    public function savePocketMoney()
    {
        $this->validate([
            'customer_pocket_money' => 'required|numeric|min:0',
            'confirm_password' => 'required',
        ]);

        // ตรวจสอบรหัสผ่าน
        if ($this->confirm_password !== env('PASSWORD_POCKET_MONEY')) {
            // ✅ โหลดค่าจริงจาก DB กลับมา แทนค่าใหม่
            $this->customer_pocket_money = CustomerModel::find($this->customerId)->customer_pocket_money;

            $this->dispatch('notify', message: 'รหัสไม่ถูกต้อง', type: 'error');
            return;
        }

        // ✅ บันทึกเมื่อรหัสถูกต้อง
        CustomerModel::where('id', $this->customerId)->update([
            'customer_pocket_money' => $this->customer_pocket_money,
        ]);

        $this->dispatch('notify', message: 'บันทึกเรียบร้อยแล้ว', type: 'success');
        $this->dispatch('closePocketMoneyModal');

        $this->confirm_password = ''; // clear input
    }

    public function updatedCustomerProvince()
    {
        $this->amphures = amphuresModel::where('province_code', $this->customer_province)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        $this->customer_amphur = '';
        $this->customer_district = '';
        $this->districts = [];
    }

    public function updatedCustomerAmphur()
    {
        $this->districts = districtsModel::where('amphur_code', $this->customer_amphur)->orderBy('district_name')->pluck('district_name', 'district_code')->toArray();

        $this->customer_district = '';
    }

    public function updatedCustomerDistrict()
    {
        $this->customer_zipcode = districtsModel::where('district_code', $this->customer_district)->value('zipcode') ?? '';
    }

    public function updatedCustomerZipcode($zip)
    {
        if (strlen($zip) !== 5) {
            return;
        }

        $districts = districtsModel::where('zipcode', $zip)->get();
        if ($districts->isEmpty()) {
            return;
        }

        $this->customer_province = $districts->first()->province_code;
        $this->updatedCustomerProvince();

        $amphurCodes = $districts->pluck('amphur_code')->unique();
        $this->amphures = amphuresModel::whereIn('amphur_code', $amphurCodes)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        if ($amphurCodes->count() === 1) {
            $this->customer_amphur = $amphurCodes->first();
            $this->updatedCustomerAmphur();

            $sameAmp = $districts->where('amphur_code', $this->customer_amphur);
            if ($sameAmp->count() === 1) {
                $this->customer_district = $sameAmp->first()->district_code;
            }
        } else {
            $this->customer_amphur = '';
            $this->customer_district = '';
            $this->districts = [];
        }
    }

    //Select Address Modal Delivery
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

    public function render()
    {
        return view('livewire.customers.customer-edit')->layout('layouts.horizontal', ['title' => 'Customers-Create']);
    }

    public function openDeliveryModal($index = null)
    {
        $this->deliveryEditIndex = $index;

        if ($index !== null && isset($this->deliveryAddresses[$index])) {
            // STEP 1: set ค่าแบบ manual เพื่อไม่ให้ reset
            $data = $this->deliveryAddresses[$index];

            $this->deliveryForm = [
                'delivery_number' => $data['delivery_number'] ?? '',
                'delivery_province' => $data['delivery_province'] ?? '',
                'delivery_amphur' => $data['delivery_amphur'] ?? '',
                'delivery_district' => $data['delivery_district'] ?? '',
                'delivery_zipcode' => $data['delivery_zipcode'] ?? '',
                'delivery_contact_name' => $data['delivery_contact_name'] ?? '',
                'delivery_phone' => $data['delivery_phone'] ?? '',
            ];

            // STEP 2: โหลด dropdown
            $this->deliveryAmphures = amphuresModel::where('province_code', $this->deliveryForm['delivery_province'])->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

            $this->deliveryDistricts = districtsModel::where('amphur_code', $this->deliveryForm['delivery_amphur'])->orderBy('district_name')->pluck('district_name', 'district_code')->toArray();
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

            $this->deliveryAmphures = [];
            $this->deliveryDistricts = [];
        }

        $this->dispatch('openModal');
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_number' => 'required',
        ]);

        // ใช้ array_merge เพื่อความมั่นใจว่าทุก key เป็น string primitive
        $form = array_merge(
            [
                'delivery_number' => '',
                'delivery_province' => '',
                'delivery_amphur' => '',
                'delivery_district' => '',
                'delivery_zipcode' => '',
                'delivery_contact_name' => '',
                'delivery_phone' => '',
            ],
            $this->deliveryForm,
        );
        // ถ้าเป็นการแก้ไขที่อยู่เดิม
        if ($this->deliveryEditIndex !== null && isset($this->deliveryAddresses[$this->deliveryEditIndex]['id'])) {
            // อัปเดตข้อมูลใน DB
            deliveryAddressModel::where('id', $this->deliveryAddresses[$this->deliveryEditIndex]['id'])->update(
                array_merge($this->deliveryForm, [
                    'customer_id' => $this->customerId,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]),
            );
        } else {
            // เพิ่มใหม่
            deliveryAddressModel::create(
                array_merge($this->deliveryForm, [
                    'customer_id' => $this->customerId,
                ]),
            );
        }
        $this->deliveryAddresses = CustomerModel::with('deliveryAddresses')->find($this->customerId)->deliveryAddresses->toArray();
        // รีเซต modal
        $this->dispatch('closeModal');
        $this->dispatch('notify', message: 'บันทึกข้อมูลเรียบร้อยแล้ว');
        $this->reset(['deliveryForm', 'deliveryEditIndex', 'showDeliveryModal']);
    }

    public function removeDelivery($index)
    {
        $address = $this->deliveryAddresses[$index] ?? null;
        $id = $address['id'] ?? null;

        if ($id) {
            deliveryAddressModel::where('id', $id)->delete();
        }

        unset($this->deliveryAddresses[$index]);
        $this->deliveryAddresses = array_values($this->deliveryAddresses);

        $this->dispatch('notify', message: 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function update()
    {
        $customer = CustomerModel::findOrFail($this->customerId);

        $customer->customer_name = $this->customer_name;
        $customer->customer_type = $this->customer_type;
        $customer->customer_level = $this->customer_level;
        $customer->customer_taxid = $this->customer_taxid;
        $customer->customer_contract_name = $this->customer_contract_name;
        $customer->customer_phone = $this->customer_phone;
        $customer->customer_email = $this->customer_email;
        $customer->customer_idline = $this->customer_idline;
        $customer->customer_address = $this->customer_address;
        $customer->customer_province = $this->customer_province;
        $customer->customer_amphur = $this->customer_amphur;
        $customer->customer_district = $this->customer_district;
        $customer->customer_zipcode = $this->customer_zipcode;

        $customer->save();

        // จัดการ delivery address
        $customer->deliveryAddresses()->delete();

        foreach ($this->deliveryAddresses as $data) {
            $customer->deliveryAddresses()->create(
                array_merge($data, [
                    'customer_id' => $customer->id,
                ]),
            );
        }

        $this->dispatch('notify', message: 'บันทึกข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}
