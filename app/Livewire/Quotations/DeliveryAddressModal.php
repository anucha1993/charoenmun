<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\customers\customerModel;
use App\Models\customers\deliveryAddressModel;

class DeliveryAddressModal extends Component
{
    public ?int $delivery_id = null;
    public ?int $customer_id = null;
    public array $items = [];
    public ?int $delivery_customer_id = null;  // เปลี่ยนเป็น int
    public string $customer_name = '';

    public array $deliveryForm = [
        'delivery_customer_id' => null,
        'delivery_contact_name' => '',
        'delivery_phone' => '',
        'delivery_address' => '',  // ใช้แค่ฟิลด์เดียวสำหรับที่อยู่เต็ม
    ];

    public bool $editing = false;
    public ?int $editing_delivery_id = null;

    #[On('edit-delivery-modal')]
    public function editDelivery($deliveryId)
    {
        $delivery = deliveryAddressModel::find($deliveryId);
        $customer = customerModel::find($delivery->customer_id);

        if ($delivery) {
            $this->editing = true;
            $this->editing_delivery_id = $deliveryId;
            $this->customer_id = $delivery->customer_id;
            $this->customer_name = $customer->customer_name ?? '';
            $this->deliveryForm = [
                'delivery_contact_name' => $delivery->delivery_contact_name ?? '',
                'delivery_phone' => $delivery->delivery_phone ?? '',
                'delivery_address' => $delivery->delivery_address ?? '', // ใช้ฟิลด์เดียว
            ];

            $this->dispatch('open-delivery-modal'); // เปิด modal
        }
    }

    public function updateDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'nullable|string|max:50',
            'deliveryForm.delivery_address' => 'nullable|string|max:1000',
        ]);

        if ($this->editing && $this->editing_delivery_id) {
            deliveryAddressModel::where('id', $this->editing_delivery_id)->update([
                'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
                'delivery_phone' => $this->deliveryForm['delivery_phone'] ?: null,
                'delivery_address' => $this->deliveryForm['delivery_address'] ?: null,
            ]);

            // รีเซ็ตฟอร์มก่อน
            $this->resetInput();
            
            // ส่ง event ให้ปิด modal ทันที
            $this->dispatch('force-close-delivery-modal');

            $this->dispatch('delivery-updated-success', [
                'customerId' => $this->customer_id,
                'deliveryId' => $this->editing_delivery_id,
            ]);

            session()->flash('success', 'อัปเดตที่อยู่จัดส่งเรียบร้อยแล้ว');
        }
    }

    public function mount()
    {
        // ไม่ต้องโหลด provinces เพราะไม่ใช้แล้ว
    }

    #[On('open-delivery-modal')]
    public function openDeliveryModal($customerId = null)
    {
        if ($customerId) {
            $customer = customerModel::find($customerId);
            if ($customer) {
                $this->customer_id = $customerId;
                $this->delivery_customer_id = $customerId;
                $this->customer_name = $customer->customer_name ?? '';
                $this->editing = false;
                $this->editing_delivery_id = null;
                $this->resetInput();
                
                // ส่ง JavaScript event เพื่อเปิด modal
                $this->dispatch('show-delivery-modal');
            }
        }
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryForm.delivery_contact_name' => 'required|string|max:255',
            'deliveryForm.delivery_phone' => 'nullable|string|max:50',
            'deliveryForm.delivery_address' => 'nullable|string|max:1000',
        ]);

        // Debug: ตรวจสอบข้อมูลก่อนบันทึก
        logger('Saving delivery', [
            'customer_id' => $this->delivery_customer_id,
            'deliveryForm' => $this->deliveryForm
        ]);

        if (!$this->delivery_customer_id) {
            session()->flash('error', 'ไม่พบข้อมูลลูกค้า กรุณาลองใหม่');
            return;
        }

        try {
            $delivery = deliveryAddressModel::create([
                'customer_id' => $this->delivery_customer_id,
                'delivery_contact_name' => $this->deliveryForm['delivery_contact_name'],
                'delivery_phone' => $this->deliveryForm['delivery_phone'] ?: null,
                'delivery_address' => $this->deliveryForm['delivery_address'] ?: null,
            ]);

            // รีเซ็ตฟอร์มก่อน
            $this->resetInput();
            
            // ส่ง event ให้ปิด modal ทันที
            $this->dispatch('force-close-delivery-modal');
            
            // แจ้ง parent ว่ามีการเพิ่มข้อมูลใหม่
            $this->dispatch('delivery-created-success', [
                'customerId' => $this->delivery_customer_id,
                'deliveryId' => $delivery->id,
            ]);
            
            session()->flash('success', 'เพิ่มที่อยู่จัดส่งเรียบร้อยแล้ว');
            
        } catch (\Exception $e) {
            logger('Error saving delivery', ['error' => $e->getMessage()]);
            session()->flash('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->dispatch('force-close-delivery-modal');
    }

    public function resetInput()
    {
        $this->deliveryForm = [
            'delivery_contact_name' => '',
            'delivery_phone' => '',
            'delivery_address' => '',
        ];
        $this->editing = false;
        $this->editing_delivery_id = null;
    }

    public function render()
    {
        return view('livewire.quotations.delivery-address-modal');
    }
}
