<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\products\ProductModel;
use App\Models\customers\CustomerModel;
use App\Models\Quotations\QuotationModel;
use App\Models\customers\deliveryAddressModel;

/**
 * Livewire Component : สร้าง / แก้ไข ใบเสนอราคา
 */
class QuotationsForm extends Component
{
    /* ──────────────── State หลัก ──────────────── */
    public Collection $customers;
    public Collection $customerDelivery;

    public ?int $customer_id = null;
    public ?int $selected_delivery_id = null;

    public ?CustomerModel $selectedCustomer = null;
    public ?deliveryAddressModel $selectedDelivery = null;

    public array $items = [];

    public float $subtotal = 0;
    public float $vat = 0;
    public float $grand_total = 0;
    public array $searchResults = [];

    public bool $enable_vat = false;
    public bool $vat_included = false;

    /* ─── ฟิลด์ใบเสนอราคา ─── */
    public ?int $quotation_id = null;
    public string $quote_number = ''; // รันอัตโนมัติเมื่อ create
    public string $quote_date; // default = วันนี้
    public string $note = '';
    public string $status = 'wait';

    /* ตัวแปรเก็บโมเดล (ถ้ามี) */
    public ?QuotationModel $quotation = null;

    public function mount(?int $id = null): void
    {
        /* default: วันนี้ */
        $this->quote_date = now()->toDateString();
        /* 1) โหมดแก้ไข --------------------------------------------------- */
        if ($id) {
            $this->quotation = QuotationModel::with('items')->find($id);
        }
        if ($this->quotation) {
            $this->fillFromModel($this->quotation);
        }
        /* 2) โหมดสร้างใหม่ ------------------------------------------------ */
        if (empty($this->items)) {
            $this->addEmptyItem(); //อย่างน้อย 1 แถวเปล่า
        }
        /* โหลด dropdown */
        $this->customers = CustomerModel::all();
        $this->customerDelivery = $this->customer_id ? deliveryAddressModel::where('customer_id', $this->customer_id)->get() : collect();
    }

    /* เติม state จากโมเดล (ใช้ได้ทั้ง mount & refresh) */
    private function fillFromModel(QuotationModel $q): void
    {
        $this->quotation_id = $q->id;
        $this->quote_number = $q->quotation_number;
        $this->customer_id = $q->customer_id;
        $this->selected_delivery_id = $q->delivery_address_id;
        $this->quote_date = $q->quote_date?->toDateString() ?? now()->toDateString();
        $this->note = $q->note ?? '';
        $this->enable_vat = $q->enable_vat;
        $this->vat_included = $q->vat_included;
        $this->status = $q->status?->value ?? 'wait';
        $this->selectedCustomer = CustomerModel::find($q->customer_id);
        $this->selectedDelivery = deliveryAddressModel::find($q->delivery_address_id);

        /* map items -> array */
        $this->items = $q->items
            ->map(function ($i) {
                $product = ProductModel::find($i->product_id);
                return [
                    'id' => $i->id,
                    'product_id' => $i->product_id,
                    'product_name' => $i->product_name,
                    'product_type' => $i->product_type,
                    'product_unit' => $i->product_unit,
                    'product_detail' => $i->product_detail ?? ($product ? $product->productType->value . ' ขนาด : ' . $product->product_size : ''), // (ถ้าเก็บใน DB)
                    'product_length' => $i->product_length,
                    'product_weight' => $i->product_weight,
                    'product_calculation' => $i->product_calculation,
                    'quantity' => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'total' => $i->total ?? $i->quantity * $i->unit_price,

                    // ✅ เพิ่มบรรทัดนี้:
                    'product_search' => $i->product_name, // แสดงชื่อใน input
                    'product_results' => [],
                    'product_results_visible' => false,
                ];
            })
            ->toArray();

        $this->calculateTotals();
    }

    /* ─────────────────── Validation ─────────────────── */
    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }

    public function updated($name, $value)
    {
        // ตรวจสอบ field ว่าเป็น product_search
        if (preg_match('/items\.(\d+)\.product_search/', $name, $matches)) {
            $index = $matches[1];

            $this->items[$index]['product_results'] = ProductModel::where('product_name', 'like', "%{$value}%")
                ->take(10)
                ->get(['product_id', 'product_name'])
                ->toArray();

            $this->items[$index]['product_results_visible'] = true;
        }
    }

    public function selectProduct($index, $productId, $productName)
    {
        $this->items[$index]['product_id'] = $productId;
        $this->items[$index]['product_search'] = $productName;
        $this->items[$index]['product_results'] = [];
        $this->items[$index]['product_results_visible'] = false;
        $this->updatedItems($productId, "items.{$index}.product_id");
    }

    /* ─────────────────── CRUD : SAVE ─────────────────── */
    public function save()
    {
        $this->calculateTotals(); // ปรับยอดล่าสุด
        $this->validate(); // ตรวจสอบข้อมูล

        DB::transaction(function () {
            $isCreate = !$this->quotation_id;

            /* 1) Running number (เฉพาะ create) */
            if ($isCreate) {
                $this->quote_number = $this->generateRunningNumber();
            }

            /* 2) Upsert Quotation */
            $q = QuotationModel::updateOrCreate(
                ['id' => $this->quotation_id],
                [
                    'quotation_number' => $this->quote_number,
                    'customer_id' => $this->customer_id,
                    'delivery_address_id' => $this->selected_delivery_id,
                    'quote_date' => $this->quote_date,
                    'note' => $this->note,
                    'subtotal' => $this->subtotal,
                    'vat' => $this->vat,
                    'grand_total' => $this->grand_total,
                    'enable_vat' => $this->enable_vat,
                    'vat_included' => $this->vat_included,
                    'status' => $this->status,
                    'created_by' => $isCreate ? Auth::id() : $this->quotation->created_by ?? Auth::id(),
                    'updated_by' => Auth::id(),
                ],
            );

            /* 3) Sync Items */
            $existingIds = [];
            foreach ($this->items as $row) {
                /* ข้ามแถวเปล่า */
                if (!$row['product_id']) {
                    continue;
                }

                $item = $q->items()->updateOrCreate(
                    ['id' => $row['id'] ?? null],
                    [
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_type' => $row['product_type'],
                        'product_unit' => $row['product_unit'],
                        'product_length' => $row['product_length'],
                        'product_weight' => $row['product_weight'],
                        'product_calculation' => $row['product_calculation'],
                        'product_detail' => $row['product_detail'],
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'total' => $row['quantity'] * $row['unit_price'],
                    ],
                );
                $existingIds[] = $item->id;
            }
            /* ลบรายการที่ผู้ใช้ลบออก */
            $q->items()->whereNotIn('id', $existingIds)->delete();

            $this->quotation_id = $q->id; // เผื่อสร้าง → แก้ต่อได้
        });

        $this->dispatch('notify', type: 'success', message: $this->quotation_id ? 'อัปเดตใบเสนอราคาเรียบร้อย' : 'สร้างใบเสนอราคาเรียบร้อย');

        return redirect()->route('quotations.index');
    }

    /* ─────────── Running Number : QTyyMM#### ─────────── */
    private function generateRunningNumber(): string
    {
        $prefix = 'QT' . now()->format('ym');
        $last = QuotationModel::where('quotation_number', 'like', $prefix . '%')->max('quotation_number');
        $next = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /* ─────────── Items helpers ─────────── */

    /** เพิ่มแถวว่าง */
    public function addEmptyItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'product_name' => '',
            'product_type' => '',
            'product_unit' => '',
            'product_length' => null,
            'product_weight' => null,
            'product_detail' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function updatedItems($value, $key)
    {
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

        if ($field === 'product_id') {
            $product = ProductModel::find($value);
            if (!$product) {
                return;
            }

            // ✅ เช็กว่ามีรายการอื่นที่ใช้ product_id เดียวกันอยู่แล้วไหม (ยกเว้น index ปัจจุบัน)
            foreach ($this->items as $i => $item) {
                if ($i != $index && $item['product_id'] == $value) {
                    // ถ้ามี → เพิ่มจำนวนที่ index นั้น แล้วลบรายการปัจจุบัน
                    $this->items[$i]['quantity'] += $this->items[$index]['quantity'] ?? 1;
                    unset($this->items[$index]);
                    $this->items = array_values($this->items);
                    $this->calculateTotals();
                    return;
                }
            }

            // ✅ ไม่มีซ้ำ → อัปเดตรายละเอียดสินค้า
            $this->items[$index]['product_detail'] = $product->productType->value . ' ขนาด : ' . $product->product_size . ' หนา :' . $product->product_calculation;
            $this->items[$index]['product_type'] = $product->productType->value;
            $this->items[$index]['product_calculation'] = $product->product_calculation;
            $this->items[$index]['product_name'] = $product->product_name;
            $this->items[$index]['unit_price'] = $product->product_price;
            $this->items[$index]['product_weight'] = $product->product_weight;
            $this->items[$index]['product_unit'] = $product->productUnit->value;
            $this->items[$index]['product_length'] = $product->product_length ?? null;
        }

        // ✅ คำนวณใหม่
        foreach ($this->items as &$item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $length = (float) ($item['product_length'] ?? 1);
            $thickness = (float) ($item['product_calculation'] ?? 1);

            if ($length <= 0) {
                $length = 1;
            }
            if ($thickness <= 0) {
                $thickness = 1;
            }

            $item['total'] = $quantity * $unitPrice * $length * $thickness;
        }

        $this->calculateTotals();
    }

    public function openDeliveryModal(int $customer_id)
    {
        $this->dispatch('open-delivery-modal', $customer_id);
    }

    /** ลบแถวสินค้า */
    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    /* ─────────── VAT & ยอดรวม ─────────── */
    // public function calculateTotals(): void
    // {
    //     $this->subtotal = collect($this->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

    //     if (!$this->enable_vat) {
    //         $this->vat = 0;
    //         $this->grand_total = $this->subtotal;
    //         return;
    //     }

    //     if ($this->vat_included) {
    //         /* VAT-In */
    //         $net = round($this->subtotal / 1.07, 2);
    //         $this->vat = round($net * 0.07, 2);
    //         $this->subtotal = $net;
    //         $this->grand_total = $net + $this->vat;
    //     } else {
    //         /* VAT-Out */
    //         $this->vat = round($this->subtotal * 0.07, 2);
    //         $this->grand_total = $this->subtotal + $this->vat;
    //     }
    // }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->items)->sum(function ($i) {
            $quantity = (float) ($i['quantity'] ?? 0);
            $unitPrice = (float) ($i['unit_price'] ?? 0);
            $length = (float) ($i['product_length'] ?? 1); // ✅ ใช้ชื่อที่ถูก
            $thickness = (float) ($i['product_calculation'] ?? 1); // ✅ ความหนา

            // ถ้าค่าเป็น 0 หรือไม่ได้กรอก ให้ถือว่าเป็น 1
            if ($length <= 0) {
                $length = 1;
            }
            if ($thickness <= 0) {
                $thickness = 1;
            }

            return $quantity * $unitPrice * $length * $thickness;
        });

        if (!$this->enable_vat) {
            $this->vat = 0;
            $this->grand_total = $this->subtotal;
            return;
        }

        if ($this->vat_included) {
            $net = round($this->subtotal / 1.07, 2);
            $this->vat = round($net * 0.07, 2);
            $this->subtotal = $net;
            $this->grand_total = $net + $this->vat;
        } else {
            $this->vat = round($this->subtotal * 0.07, 2);
            $this->grand_total = $this->subtotal + $this->vat;
        }
    }

    public function updatedEnableVat(): void
    {
        $this->calculateTotals();
    }
    public function updatedVatIncluded(): void
    {
        $this->calculateTotals();
    }

    /* ─────────── Customer / Delivery helpers ─────────── */

    #[On('customer-created-success')]
    public function handleCustomerCreatedSuccess(array $payload): void
    {
        $this->customer_id = (int) ($payload['customerId'] ?? 0);
        $this->refreshCustomers();
    }

    public function setCustomerId($id): void
    {
        $this->customer_id = $id;
        $this->updatedCustomerId($id);
    }

    public function updatedCustomerId($value): void
    {
        $this->selectedCustomer = CustomerModel::find($value);
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $value)->get();
        $this->selected_delivery_id = null;
        $this->selectedDelivery = null;
    }

    public function updatedSelectedDeliveryId($id): void
    {
        $this->selectedDelivery = deliveryAddressModel::find($id);
    }

    #[On('delivery-updated-success')]
    public function handleDeliveryUpdatedSuccess(array $payload)
    {
        $this->selected_delivery_id = $payload['deliveryId'] ?? null;
    }

    public function refreshCustomers(): void
    {
        $this->customers = CustomerModel::all();
        if ($this->customer_id) {
            $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();
            $this->updatedCustomerId($this->customer_id);
        }

        $this->dispatch('$refresh');
    }

    public function getIsCreateProperty(): bool
    {
        return $this->quotation_id === null; // true = โหมดสร้าง
    }

    /* ─────────── Livewire Render ─────────── */
    public function render()
    {
        $products = ProductModel::all();
        return view('livewire.quotations.quotations-form', compact('products'))->layout('layouts.horizontal', ['title' => 'Quotations']);
    }
}
