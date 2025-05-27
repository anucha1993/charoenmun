<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use App\Models\products\ProductModel;
use App\Models\customers\CustomerModel;
use App\Models\customers\deliveryAddressModel;

class QuotationsForm extends Component
{
    public Collection $customers;
    public Collection $customerDelivery;

    public ?int $customer_id = null;
    public ?int $selected_delivery_id = null;

    public ?CustomerModel $selectedCustomer = null;
    public ?deliveryAddressModel $selectedDelivery = null;
    public array $customer = [];
    // protected $listeners = ['customer-saved' => 'refreshCustomerFromModal'];
    public ?int $selectedCustomerId = null;

    public array $items = [];
    public float $subtotal = 0;
    public float $vat = 0;
    public float $grand_total = 0;

    public bool $vat_included = false;
    public bool $enable_vat = false;

    public function mount()
    {
        $this->addEmptyItem();
        $this->customers = CustomerModel::all();
        $this->customerDelivery = collect();
    }

    #[On('customer-created-success')]
    public function handleCustomerCreatedSuccess(array $payload)
    {
        $this->customer_id = (int) ($payload['customerId'] ?? 0);
        $this->refreshCustomers(); // ✅ reuse method ที่โหลดข้อมูลใหม่จริง
    }

    public function openDeliveryModal(int $customer_id)
    {
        $this->dispatch('open-delivery-modal', $customer_id);
    }

    public function reloadCustomerListAndSelect($customerId)
    {
        $this->customers = CustomerModel::all();
        $this->customer_id = (int) $customerId;
        $this->updatedCustomerId($this->customer_id);
    }

    public function updatedCustomerId($value)
    {
        $this->selectedCustomer = CustomerModel::find($value);
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $value)->get();

        $this->selected_delivery_id = null;
        $this->selectedDelivery = null;
    }

    public function updatedSelectedDeliveryId($id)
    {
        $this->selectedDelivery = deliveryAddressModel::find($id);
    }

    #[On('delivery-created-success')]
    public function reloadCustomerDeliveryList(array $payload)
    {
        $customerId = $payload['customerId'] ?? null;
        $deliveryId = $payload['deliveryId'] ?? null;

        if ($customerId) {
            $this->customer_id = $customerId;

            // โหลดใหม่จาก DB ทันที
            $this->customerDelivery = deliveryAddressModel::where('customer_id', $customerId)->get();

            if ($deliveryId) {
                $this->selected_delivery_id = $deliveryId;
                $this->selectedDelivery = deliveryAddressModel::find($deliveryId);
            } else {
                $last = deliveryAddressModel::where('customer_id', $customerId)->latest('id')->first();
                $this->selected_delivery_id = $last?->id;
                $this->selectedDelivery = $last;
            }

            $this->dispatch('$refresh');
        }
    }

    #[On('delivery-updated-success')]
    public function UpdatereloadCustomerDeliveryList(array $payload)
    {
        $customerId = $payload['customerId'] ?? null;
        $deliveryId = $payload['deliveryId'] ?? null;

        if ($customerId) {
            $this->customer_id = $customerId;

            // โหลดใหม่จาก DB ทันที
            $this->customerDelivery = deliveryAddressModel::where('customer_id', $customerId)->get();

            if ($deliveryId) {
                $this->selected_delivery_id = $deliveryId;
                $this->selectedDelivery = deliveryAddressModel::find($deliveryId);
            } else {
                $last = deliveryAddressModel::where('customer_id', $customerId)->latest('id')->first();
                $this->selected_delivery_id = $last?->id;
                $this->selectedDelivery = $last;
            }

            $this->dispatch('$refresh');
        }
    }

    public function setCustomerId($id)
    {
        $this->customer_id = $id;
        $this->updatedCustomerId($id); // เรียกใช้ logic เดิม
    }

    public function refreshCustomers()
    {
        $this->customers = CustomerModel::all();

        if ($this->customer_id) {
            $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();
            $this->updatedCustomerId($this->customer_id);
        }

        $this->dispatch('$refresh'); // ✅ บอก Livewire render ใหม่
    }

    /// Qutations

    public function addEmptyItem()
    {
        $this->items[] = [
            'product_id' => null,
            'product_unit' => null,
            'product_detail' => '',
            'product_name' => '',
            'product_type' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'product_weight' => '',
            'total' => 0,
        ];
    }

    public function updatedItems($value, $key)
    {
        // ดึง index และ field ที่ถูกอัปเดต เช่น items.0.product_id
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

        if ($field === 'product_id') {
            $product = ProductModel::find($value);
            if ($product) {
                $this->items[$index]['product_detail'] = $product->productType->value . ' ขนาด : ' . $product->product_size;
                $this->items[$index]['product_type'] = $product->productType->value;
                $this->items[$index]['product_name'] = $product->product_name;
                $this->items[$index]['unit_price'] = $product->product_price;
                $this->items[$index]['product_weight'] = $product->product_weight;
                $this->items[$index]['product_unit'] = $product->productUnit->value;
                $this->items[$index]['product_length'] = $product->product_length;
            }
        }

        // คำนวณยอดรวมใหม่
        foreach ($this->items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }

        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // รีจัด index
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        if (!$this->enable_vat) {
            $this->vat = 0;
            $this->grand_total = $this->subtotal;
            return;
        }

        if ($this->vat_included) {
            // VAT รวมใน
            $this->subtotal = round($this->subtotal / 1.07, 2);
            $this->vat = round($this->subtotal * 0.07, 2);
            $this->grand_total = $this->subtotal + $this->vat;
        } else {
            // VAT แยกนอก
            $this->vat = round($this->subtotal * 0.07, 2);
            $this->grand_total = $this->subtotal + $this->vat;
        }
    }

    public function updatedEnableVat()
    {
        $this->calculateTotals();
    }

    public function updatedVatIncluded()
    {
        $this->calculateTotals();
    }

    public function render()
    {
        $products = ProductModel::all();
        return view('livewire.quotations.quotations-form', compact('products'))->layout('layouts.horizontal', ['title' => 'Quotations']);
    }
}
