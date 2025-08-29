<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\customers\customerModel;
use App\Models\addressList\amphuresModel;
use App\Models\globalsets\GlobalSetModel;
use App\Models\globalsets\GlobalSetValueModel;
use App\Models\addressList\districtsModel;
use App\Models\addressList\provincesModel;
use App\Models\customers\deliveryAddressModel;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderPayment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerEdit extends Component
{
    public $customerId;
    public $totalOrders = 0;
    public $totalOrderAmount = 0;
    public $pendingDeliveries = 0;
    public $paymentHistory;

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
        $customer_address = '';

    public array $deliveryAddresses = [];

    // Modal และการจัดการฟอร์มที่อยู่
    public bool $showDeliveryModal = false;
    public ?int $deliveryEditIndex = null;
    public array $deliveryForm = [];

    //Select
    public array $customerType = [];  // กำหนดให้เป็น array
    public array $customerLevel = []; // กำหนดให้เป็น array
    public array $provinces = [],
        $amphures = [],
        $districts = [];
    protected $listeners = ['removeDelivery'];
    public float $customer_pocket_money = 0;
    public string $confirm_password = '';

    private function loadCustomerStats()
    {
        if (!$this->customerId) return;

        // ยอดคำสั่งซื้อและจำนวนใบสั่งซื้อ
        $orderStats = OrderModel::where('customer_id', $this->customerId)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(order_grand_total) as total_amount')
            )->first();
        
        $this->totalOrders = $orderStats->total_orders ?? 0;
        $this->totalOrderAmount = $orderStats->total_amount ?? 0;

        // สินค้ารอส่ง (นับจากออเดอร์ที่ยังไม่ completed)
        $this->pendingDeliveries = OrderModel::where('customer_id', $this->customerId)
            ->whereNotIn('order_status', ['completed'])
            ->count();

        // ประวัติการชำระเงิน (รวมทุกประเภท)
        $payments = OrderPayment::whereHas('order', function($query) {
            $query->where('customer_id', $this->customerId);
        })
        ->with(['order' => function($query) {
            $query->select('id', 'order_number');
        }])
        ->select('id', 'order_id', 'payment_type', 'amount', 'status', 'slip_path', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get();

        // แปลงเป็น array และจัดกลุ่มด้วย vanilla PHP
        $this->paymentHistory = [];
        foreach ($payments as $payment) {
            if (!isset($this->paymentHistory[$payment->payment_type])) {
                $this->paymentHistory[$payment->payment_type] = [];
            }
            $this->paymentHistory[$payment->payment_type][] = $payment->toArray();
        }
    }

    public function mount()
    
    {
        if ($this->customerId) {
            $this->loadCustomerStats();

            // โหลดข้อมูลลูกค้าพร้อม relationship
            $customer = customerModel::with(['deliveryAddresses' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($this->customerId);
            
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
        }

        // โหลด customer types และ levels จาก global_sets
        $customerTypes = GlobalSetValueModel::select(['id', 'value'])
            ->where('global_set_id', 1)
            ->where('status', 'Enable')
            ->orderBy('sort_order')
            ->get();

        $this->customerType = $customerTypes->map(function($item) {
            return [
                'id' => $item->id,
                'value' => $item->value
            ];
        })->all();

        // โหลด customer levels
        $customerLevels = GlobalSetValueModel::select(['id', 'value'])
            ->where('global_set_id', 2)
            ->where('status', 'Enable')
            ->orderBy('sort_order')
            ->get();

        $this->customerLevel = $customerLevels->map(function($item) {
            return [
                'id' => $item->id,
                'value' => $item->value
            ];
        })->all();

        // โหลดที่อยู่จัดส่ง
        if (isset($customer)) {
            $addresses = deliveryAddressModel::where('customer_id', $customer->id)
                ->select(['id', 'delivery_number', 'delivery_address', 'delivery_contact_name', 'delivery_phone'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'delivery_number' => $item->delivery_number,
                        'delivery_address' => $item->delivery_address,
                        'delivery_contact_name' => $item->delivery_contact_name,
                        'delivery_phone' => $item->delivery_phone,
                    ];
                })->all();
            
            $this->deliveryAddresses = $addresses;
        } else {
            $this->deliveryAddresses = [];
        }
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
            $this->customer_pocket_money = customerModel::find($this->customerId)->customer_pocket_money;

            $this->dispatch('notify', message: 'รหัสไม่ถูกต้อง', type: 'error');
            return;
        }

        // ✅ บันทึกเมื่อรหัสถูกต้อง
        customerModel::where('id', $this->customerId)->update([
            'customer_pocket_money' => $this->customer_pocket_money,
        ]);

        $this->dispatch('notify', message: 'บันทึกเรียบร้อยแล้ว', type: 'success');
        $this->dispatch('closePocketMoneyModal');

        $this->confirm_password = ''; // clear input
    }

    // ลบเมธอดที่เกี่ยวกับที่อยู่ทั้งหมด เนื่องจากไม่ได้ใช้แล้ว

    public function render()
    {
        return view('livewire.customers.customer-edit');
    }

    public function openDeliveryModal($index = null)
    {
        $this->deliveryEditIndex = $index;

        if ($index !== null && isset($this->deliveryAddresses[$index])) {
            // STEP 1: set ค่าแบบ manual เพื่อไม่ให้ reset
            $data = $this->deliveryAddresses[$index];

            $this->deliveryForm = [
                'delivery_number' => $data['delivery_number'] ?? '',
                'delivery_address' => $data['delivery_address'] ?? '',
                'delivery_contact_name' => $data['delivery_contact_name'] ?? '',
                'delivery_phone' => $data['delivery_phone'] ?? '',
            ];
        } else {
            $this->deliveryForm = [
                'delivery_number' => '',
                'delivery_address' => '',
                'delivery_contact_name' => '',
                'delivery_phone' => '',
            ];
        }

        $this->dispatch('openModal');
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_number' => 'required|string|max:255',
            'deliveryForm.delivery_address' => 'required|string|max:1000',
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'required|string|max:50',
        ]);

        // ใช้ array_merge เพื่อความมั่นใจว่าทุก key เป็น string primitive
        $form = array_merge(
            [
                'delivery_number' => '',
                'delivery_address' => '',
                'delivery_contact_name' => '',
                'delivery_phone' => '',
            ],
            $this->deliveryForm,
        );
        // ถ้าเป็นการแก้ไขที่อยู่เดิม
        if ($this->deliveryEditIndex !== null && isset($this->deliveryAddresses[$this->deliveryEditIndex]['id'])) {
            // อัปเดตข้อมูลใน DB
            $addressId = $this->deliveryAddresses[$this->deliveryEditIndex]['id'] ?? null;
            if ($addressId) {
                deliveryAddressModel::where('id', $addressId)->update(
                    array_merge($this->deliveryForm, [
                        'customer_id' => $this->customerId,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ])
                );
            }
        } else {
            // เพิ่มใหม่
            deliveryAddressModel::create(
                array_merge($this->deliveryForm, [
                    'customer_id' => $this->customerId,
                ]),
            );
        }
        // ดึงข้อมูล delivery addresses ใหม่
        $addresses = deliveryAddressModel::where('customer_id', $this->customerId)
            ->select(['id', 'delivery_number', 'delivery_address', 'delivery_contact_name', 'delivery_phone'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'delivery_number' => $item->delivery_number,
                    'delivery_address' => $item->delivery_address,
                    'delivery_contact_name' => $item->delivery_contact_name,
                    'delivery_phone' => $item->delivery_phone,
                ];
            })->all();
        
        $this->deliveryAddresses = $addresses;
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
        $customer = customerModel::findOrFail($this->customerId);

        $customer->customer_name = $this->customer_name;
        $customer->customer_type = $this->customer_type;
        $customer->customer_level = $this->customer_level;
        $customer->customer_taxid = $this->customer_taxid;
        $customer->customer_contract_name = $this->customer_contract_name;
        $customer->customer_phone = $this->customer_phone;
        $customer->customer_email = $this->customer_email;
        $customer->customer_idline = $this->customer_idline;
        $customer->customer_address = $this->customer_address;

        $customer->save();

        // จัดการ delivery address
        $customer->deliveryAddresses()->delete();

        foreach ($this->deliveryAddresses as $data) {
            // Remove any existing ID to prevent issues with create
            unset($data['id']);
            $customer->deliveryAddresses()->create(
                array_merge($data, [
                    'customer_id' => $customer->id,
                ]),
            );
        }

        $this->dispatch('notify', message: 'บันทึกข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}
