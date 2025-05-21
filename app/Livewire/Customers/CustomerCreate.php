<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\{addressList\provincesModel, addressList\amphuresModel, addressList\districtsModel, customers\customerModel, customers\deliveryAddressModel, globalsets\GlobalSetModel};

class CustomerCreate extends Component
{
    public ?int $customer_id = null;
    public array $customerType = [];
    public array $customerLevel = [];
    public array $provinces = [],
                 $amphures = [],
                 $districts = [];
    public $customer_province = '',
        $customer_amphur = '',
        $customer_district = '',
        $customer_type = '',
        $customer_level = '',
        $customer_taxid = '',
        $customer_contract_name = '',
        $customer_phone = '',
        $customer_email = '',
        $customer_idline = '',
        $customer_address = '',
        $customer_name = '',
        $customer_zipcode = '';

    /* -------- ที่อยู่จัดส่ง (ชั่วคราว) -------- */
    public array $pendingDeliveries = []; // ⭐ แสดงในตาราง & เตรียม insert
    public string $activeTab = 'home';

    /* -------------------- mount -------------------- */
    public function mount(?int $id = null)
    {
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        // 2) โหลด Global Set (customerType & customerLevel)
        $set = GlobalSetModel::with('values')->find(1);
        $setLevel = GlobalSetModel::with('values')->find(2);
        $this->customerType = $set ? $set->values->where('status', 'Enable')->pluck('value')->toArray() : [];
        $this->customerLevel = $setLevel ? $setLevel->values->where('status', 'Enable')->pluck('value')->toArray() : [];

        // 3) ถ้ามี $id ⇒ กำลังแก้ไขลูกค้าเดิม
        if ($id) {
            $this->customer_id = $id;

            // อ่านข้อมูลลูกค้า
            $cust = customerModel::with('deliveryAddresses')->findOrFail($id);

            // ─── ตั้งค่าฟิลด์หลัก ───
            $this->customer_name = $cust->customer_name;
            $this->customer_type = $cust->customer_type;
            $this->customer_level = $cust->customer_level;
            $this->customer_taxid = $cust->customer_taxid;
            $this->customer_contract_name = $cust->customer_contract_name;
            $this->customer_phone = $cust->customer_phone;
            $this->customer_email = $cust->customer_email;
            $this->customer_idline = $cust->customer_idline;
            $this->customer_address = $cust->customer_address;
            $this->customer_address = $cust->customer_address;

            // ─── ตั้ง dropdown ที่อยู่หลัก ───
            $this->customer_province = $cust->customer_province;
            $this->updatedCustomerProvince(); // โหลด amphures ตามจังหวัด
            $this->customer_amphur = $cust->customer_amphur;
            $this->updatedCustomerAmphur(); // โหลด districts ตามอำเภอ
            $this->customer_district = $cust->customer_district;
            $this->updatedCustomerDistrict(); // ตั้ง zipcode

            // ─── โหลดที่อยู่จัดส่งเดิมลงตาราง ───
            // (Model มี $appends => ชื่อจังหวัด/อำเภอ/ตำบล จะมาโดยอัตโนมัติ)
            $this->pendingDeliveries = $cust->deliveryAddresses->toArray();

            // สลับไปยังแท็บ “profile” ให้อัตโนมัติ
            $this->activeTab = 'home';
        }
    }
    /* ---------- LISTENER รับ event จาก Child ---------- */
    #[On('delivery-temp-added')]
    public function addTempDelivery($number, $province_code, $province_name, $amphur_code, $amphur_name, $district_code, $district_name, $zipcode, $contact_name, $phone)
    {
        $this->pendingDeliveries[] = [
            'number' => $number,
            'delivery_province' => $province_code,
            'delivery_province_name' => $province_name, // ⭐
            'delivery_amphur' => $amphur_code,
            'delivery_amphur_name' => $amphur_name, // ⭐
            'delivery_district' => $district_code,
            'delivery_district_name' => $district_name, // ⭐
            'delivery_zipcode' => $zipcode,
            'delivery_contact_name' => $contact_name,
            'delivery_phone' => $phone,
        ];
        $this->activeTab = 'profile';
    }

    public function removeTempDelivery($index)
    {
        unset($this->pendingDeliveries[$index]);
        $this->pendingDeliveries = array_values($this->pendingDeliveries);
    }

    /* ---------- dropdown อ้างชื่อ method ให้ตรง property ---------- */
    public function updatedCustomerProvince()
    {
        $this->amphures = amphuresModel::where('province_code', $this->customer_province)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        $this->customer_amphur = $this->customer_district = '';
        $this->districts = [];
    }

    public function updatedCustomerAmphur()
    {
        /* โหลดเฉพาะตำบลของอำเภอที่เลือก */
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

        /* 1) ดึงตำบลทั้งหมดของ ZIP นี้ */
        $districts = districtsModel::where('zipcode', $zip)->get();
        if ($districts->isEmpty()) {
            return;
        }

        /* 2) ตั้งจังหวัด (ทุกแถว province_code เหมือนกัน) */
        $this->customer_province = $districts->first()->province_code;
        $this->updatedCustomerProvince(); // โหลด amphures ของจังหวัดนี้

        /* 3) กรองรายชื่ออำเภอเฉพาะที่อยู่ใน ZIP */
        $amphurCodes = $districts->pluck('amphur_code')->unique();
        $this->amphures = amphuresModel::whereIn('amphur_code', $amphurCodes)->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        /* 4) ถ้ามีแค่อำเภอเดียว → เลือกให้อัตโนมัติ, แล้วโหลดตำบล */
        if ($amphurCodes->count() === 1) {
            $this->customer_amphur = $amphurCodes->first();
            $this->updatedCustomerAmphur(); // โหลดตำบลของอำเภอนั้น

            /* ตั้ง district ถ้ามีแค่ตำบลเดียว */
            $sameAmp = $districts->where('amphur_code', $this->customer_amphur);
            if ($sameAmp->count() === 1) {
                $this->customer_district = $sameAmp->first()->district_code;
            }
        } else {
            /* มีหลายอำเภอ → ให้ผู้ใช้เลือกเอง */
            $this->customer_amphur = '';
            $this->customer_district = '';
            $this->districts = []; // รอเลือกอำเภอก่อนแล้วค่อยโชว์ตำบล
        }
    }

    public function saveCustomer()
    {
        /* validate ... */

        $customer = CustomerModel::updateOrCreate(
            ['id' => $this->customer_id],
            [
                // ฟิลด์อื่น รับจาก this->...
                'customer_name' => $this->customer_name,
                'customer_type' => $this->customer_type,
                'customer_level' => $this->customer_level,
                'customer_taxid' => $this->customer_taxid,
                'customer_contract_name' => $this->customer_contract_name,
                'customer_phone' => $this->customer_phone,
                'customer_email' => $this->customer_email,
                'customer_idline' => $this->customer_idline,
                'customer_address' => $this->customer_address,
                'customer_province' => $this->customer_province,
                'customer_amphur' => $this->customer_amphur,
                'customer_district' => $this->customer_district,
                'customer_zipcode' => $this->customer_zipcode,
            ],
        );

        /* sync delivery addresses */
        $customer->deliveryAddresses()->delete();
        $customer->deliveryAddresses()->createMany(collect($this->pendingDeliveries)->map(fn($d) => \Illuminate\Support\Arr::except($d, ['delivery_province_name', 'delivery_amphur_name', 'delivery_district_name']))->all());

        $this->dispatch('notify', type: 'success', text: 'บันทึกเรียบร้อย');
        return redirect()->route('customers.index'); // ไปหน้า index
    }

    public function render()
    {
        return view('livewire.customers.customer-create')->layout('layouts.horizontal', ['title' => 'Customers-Create']);
    }
}
