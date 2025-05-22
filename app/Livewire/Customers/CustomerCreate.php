<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\customers\CustomerModel;
use App\Models\addressList\amphuresModel;
use App\Models\globalsets\GlobalSetModel;
use App\Models\addressList\districtsModel;
use App\Models\addressList\provincesModel;

class CustomerCreate extends Component
{
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

    public array $provinces = [], $amphures = [], $districts = [];

    public Collection $customerType;
    public Collection $customerLevel;

    public function mount()
    {
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();

        $set = GlobalSetModel::with('values')->find(1); // ประเภทลูกค้า
        $setLevel = GlobalSetModel::with('values')->find(2); // ระดับลูกค้า
       $this->customerType = $set?->values->where('status', 'Enable')->values() ?? collect();
       $this->customerLevel = $setLevel?->values->where('status', 'Enable')->values() ?? collect();
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
        if (strlen($zip) !== 5) return;

        $districts = districtsModel::where('zipcode', $zip)->get();
        if ($districts->isEmpty()) return;

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

    public function save()
    {
        $this->validate([
            'customer_name' => 'required',
            'customer_type' => 'required',
            'customer_level' => 'required',
        ]);

        $customer = CustomerModel::create([
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
        ]);

        return redirect()->route('customers.edit', $customer->id)
            ->with('success', 'เพิ่มลูกค้าเรียบร้อยแล้ว กรุณาเพิ่มที่อยู่จัดส่ง');
    }

    public function render()
    {
        return view('livewire.customers.customer-create')->layout('layouts.horizontal', ['title' => 'Customers - Create']);
    }
}
