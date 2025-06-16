<?php
namespace App\Livewire\Quotations;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\customers\customerModel;
use App\Models\addressList\amphuresModel;
use App\Models\globalsets\GlobalSetModel;
use App\Models\addressList\districtsModel;
use App\Models\addressList\provincesModel;
use Livewire\Attributes\On; // ✅ จำเป็นสำหรับ #[On(...)]

class CustomerModal extends Component
{
    public ?int $customer_id = null;
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
    public Collection $customerType;
    public Collection $customerLevel;
    public array $provinces = [],
        $amphures = [],
        $districts = [];

    public bool $isDuplicateCustomer = false;
    public string $duplicateMessage = '';

    public function checkDuplicateCustomer()
    {
        if (!$this->customer_name || !$this->customer_taxid) {
            $this->isDuplicateCustomer = false;
            $this->duplicateMessage = '';
            return;
        }

        $query = CustomerModel::query()->where('customer_name', $this->customer_name)->where('customer_taxid', $this->customer_taxid);

        if (isset($this->customerId)) {
            $query->where('id', '!=', $this->customerId);
        }

        $this->isDuplicateCustomer = $query->exists();

        if ($this->isDuplicateCustomer) {
            $this->duplicateMessage = '❌ พบข้อมูลลูกค้าชื่อนี้และเลขภาษีนี้ในระบบแล้ว';
        } else {
            $this->duplicateMessage = '';
        }
    }


    public function updatedCustomerTaxid()
    {
        $this->checkDuplicateCustomer();
    }

    public function updatedCustomerName()
    {
        $this->checkDuplicateCustomer();
    }



    public function mount(?int $customer_id = null)
    {
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        $set = GlobalSetModel::with('values')->find(1); // ประเภทลูกค้า
        $setLevel = GlobalSetModel::with('values')->find(2); // ระดับลูกค้า
        $this->customerType = $set?->values->where('status', 'Enable')->values() ?? collect();
        $this->customerLevel = $setLevel?->values->where('status', 'Enable')->values() ?? collect();

        $this->customer_id = $customer_id;
        if ($this->customer_id) {
            $this->loadCustomer($this->customer_id);
        }
    }

    #[On('edit-customer')]
    public function editCustomer(int $id)
    {
        $this->loadCustomer($id);
        $this->dispatch('open-customer-modal'); // ✅ เปิด modal
    }

    #[On('create-customer')]
    public function createCustomer()
    {
        $this->reset(['customer_id', 'customer_code', 'customer_name', 'customer_type', 'customer_level', 'customer_taxid', 'customer_contract_name', 'customer_phone', 'customer_email', 'customer_idline', 'customer_address', 'customer_province', 'customer_amphur', 'customer_district', 'customer_zipcode', 'amphures', 'districts']);
        // ต้องโหลด select ตัวเลือกอีกครั้ง
        $this->provinces = provincesModel::orderBy('province_name')->pluck('province_name', 'province_code')->toArray();
        $set = GlobalSetModel::with('values')->find(1); // ประเภทลูกค้า
        $setLevel = GlobalSetModel::with('values')->find(2); // ระดับลูกค้า
        $this->customerType = $set?->values->where('status', 'Enable')->values() ?? collect();
        $this->customerLevel = $setLevel?->values->where('status', 'Enable')->values() ?? collect();
        $this->dispatch('open-customer-modal');
    }
    



    public function loadCustomer(int $id)
    {
        $customer = CustomerModel::find($id);

        if ($customer) {
            $this->customer_id = $customer->id;
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
            $this->amphures = amphuresModel::where('province_code', $this->customer_province)->pluck('amphur_name', 'amphur_code')->toArray();
            $this->customer_amphur = $customer->customer_amphur;
            $this->districts = districtsModel::where('amphur_code', $this->customer_amphur)->pluck('district_name', 'district_code')->toArray();
            $this->customer_district = $customer->customer_district;
            $this->customer_zipcode = districtsModel::where('district_code', $this->customer_district)->value('zipcode') ?? '';
            $this->customer_zipcode = $customer->customer_zipcode;
        }
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

    //Update
    public function update()
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_level' => 'required|string|max:255',
            'customer_type' => 'required|string|max:255',
        ]);
        $customer = CustomerModel::updateOrCreate(
            ['id' => $this->customer_id],
            [
                'customer_type' => $this->customer_type,
                'customer_level' => $this->customer_level,
                'customer_name' => $this->customer_name,
                'customer_contract_name' => $this->customer_contract_name,
                'customer_code' => $this->customer_code,
                'customer_address' => $this->customer_address,
                'customer_taxid' => $this->customer_taxid,
                'customer_province' => $this->customer_province,
                'customer_amphur' => $this->customer_amphur,
                'customer_district' => $this->customer_district,
                'customer_zipcode' => $this->customer_zipcode,
                'customer_phone' => $this->customer_phone,
                'customer_email' => $this->customer_email,
                'customer_idline' => $this->customer_idline,
            ],
        )->fresh();

        $this->dispatch('close-customer-modal');
        if ($customer && $customer->id) {
            $this->dispatch('customer-created-success', ['customerId' => $customer->id]);
        } else {
            logger('❗ Customer creation failed or missing ID', ['customer' => $customer]);
        }
    }

    public function render()
    {
        return view('livewire.quotations.customer-modal');
    }
}
