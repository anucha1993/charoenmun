<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\{
    addressList\provincesModel,
    addressList\amphuresModel,
    addressList\districtsModel,
    customers\customerModel,
    globalsets\GlobalSetModel
};
use Livewire\Attributes\On;
class CustomerEdit extends Component
{
  

    public array $customerType = [];
    public array $customerLevel = [];

    public array $provinces = [], $amphures = [], $districts = [];

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

    public array $pendingDeliveries = [];
    public string $activeTab = 'home';
     public ?int $customer_id = null;

    public function mount(int $id)
    {
        $this->customer_id = $id;
       // dd($this->customer_id);

        // โหลด dropdown จังหวัด, ประเภท, ระดับ
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        $set = GlobalSetModel::with('values')->find(1);
        $setLevel = GlobalSetModel::with('values')->find(2);
        $this->customerType = $set?->values->where('status', 'Enable')->pluck('value')->toArray() ?? [];
        $this->customerLevel = $setLevel?->values->where('status', 'Enable')->pluck('value')->toArray() ?? [];

        // โหลดข้อมูลลูกค้า
        $cust = customerModel::with('deliveryAddresses')->findOrFail($id);

        $this->customer_name = $cust->customer_name;
        $this->customer_type = $cust->customer_type;
        $this->customer_level = $cust->customer_level;
        $this->customer_taxid = $cust->customer_taxid;
        $this->customer_contract_name = $cust->customer_contract_name;
        $this->customer_phone = $cust->customer_phone;
        $this->customer_email = $cust->customer_email;
        $this->customer_idline = $cust->customer_idline;
        $this->customer_address = $cust->customer_address;

        $this->customer_province = $cust->customer_province;
        $this->updatedCustomerProvince();

        $this->customer_amphur = $cust->customer_amphur;
        $this->updatedCustomerAmphur();

        $this->customer_district = $cust->customer_district;
        $this->updatedCustomerDistrict();

        $this->customer_zipcode = $cust->customer_zipcode;

        $this->pendingDeliveries = $cust->deliveryAddresses->toArray();
        $this->activeTab = 'home';
    }

    public function updatedCustomerProvince()
    {
        $this->amphures = amphuresModel::where('province_code', $this->customer_province)
            ->orderBy('amphur_name')->pluck('amphur_name', 'amphur_code')->toArray();

        $this->customer_amphur = '';
        $this->customer_district = '';
        $this->districts = [];
    }

    public function updatedCustomerAmphur()
    {
        $this->districts = districtsModel::where('amphur_code', $this->customer_amphur)
            ->orderBy('district_name')->pluck('district_name', 'district_code')->toArray();

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

    public function saveCustomer()
    {
        $this->validate([
            'customer_name' => 'required',
            'customer_contract_name' => 'required',
            'customer_phone' => 'required',
            'customer_idline' => 'required',
            'customer_type' => 'required',
            'customer_level' => 'required',
            'customer_province' => 'required',
            'customer_amphur' => 'required',
            'customer_district' => 'required',
            'customer_zipcode' => 'required|max:5'
        ]);

        $customer = customerModel::findOrFail($this->customer_id);
        $customer->update([
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

        $customer->deliveryAddresses()->delete();
        $customer->deliveryAddresses()->createMany(collect($this->pendingDeliveries)->map(
            fn($d) => \Illuminate\Support\Arr::except($d, ['delivery_province_name', 'delivery_amphur_name', 'delivery_district_name'])
        )->all());

        $this->dispatch('notify', type: 'success', text: 'บันทึกเรียบร้อย');
        return redirect()->route('customers.index');
    }

   #[On('delivery-temp-added')]
public function addTempDelivery(
    $number,
    $delivery_province,
    $delivery_province_name,
    $delivery_amphur,
    $delivery_amphur_name,
    $delivery_district,
    $delivery_district_name,
    $delivery_zipcode,
    $delivery_contact_name,
    $delivery_phone,
) {
    $this->pendingDeliveries[] = [
        'number' => $number,
        'delivery_province' => $delivery_province,
        'delivery_province_name' => $delivery_province_name,
        'delivery_amphur' => $delivery_amphur,
        'delivery_amphur_name' => $delivery_amphur_name,
        'delivery_district' => $delivery_district,
        'delivery_district_name' => $delivery_district_name,
        'delivery_zipcode' => $delivery_zipcode,
        'delivery_contact_name' => $delivery_contact_name,
        'delivery_phone' => $delivery_phone,
    ];
}



public function render()
{
    return view('livewire.customers.customer-edit', [
        'customer_id' => $this->customer_id // ⭐ ส่งให้ blade ใช้งาน
    ])->layout('layouts.horizontal', ['title' => 'Customers-Edit']);
}

}
