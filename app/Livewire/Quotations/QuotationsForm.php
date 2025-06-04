<?php

namespace App\Livewire\Quotations;

use Livewire\Component;
<<<<<<< HEAD
use Livewire\Attributes\On;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\products\ProductModel;
use App\Models\Orders\OrderItemsModel;
=======
use Illuminate\Support\Collection;
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09
use App\Models\customers\CustomerModel;
use App\Models\customers\deliveryAddressModel;
use Livewire\Attributes\On;

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

<<<<<<< HEAD
    public array $items = [];

    public float $quote_subtotal = 0;
    public float $quote_vat = 0;
    public float $quote_discount = 0;
    public float $quote_grand_total = 0;
    public array $searchResults = [];
    public float $netSubtotal = 0;

    public bool $quote_enable_vat = false;
    public bool $quote_vat_included = false;

    /* ─── ฟิลด์ใบเสนอราคา ─── */
    public ?int $quotation_id = null;
    public string $quote_number = ''; // รันอัตโนมัติเมื่อ create
    public string $quote_date; // default = วันนี้
    public string $quote_note = '';
    public string $quote_status = 'wait';

    /* ตัวแปรเก็บโมเดล (ถ้ามี) */
    public ?QuotationModel $quotation = null;


    public function mount(?int $id = null): void
    {
        /* default: วันนี้ */
        $this->quote_date = now()->toDateString();
        /* 1) โหมดแก้ไข --------------------------------------------------- */
        if ($id) {
            $this->quotation_id = $id;
            $quotation = QuotationModel::with(['items', 'customer', 'deliveryAddress', 'sale'])->find($id);
            if ($quotation) {
                $this->quote_number = $quotation->quote_number;
                $this->quote_status = $quotation->quote_status;
                // … เติม state อื่น ๆ จาก $quotation …
            }
        }
        if ($this->quotation) {
            $this->fillFromModel($this->quotation);
        }
        /* 2) โหมดสร้างใหม่ ------------------------------------------------ */
        if (empty($this->items)) {
            $this->addEmptyItem(); //อย่างน้อย 1 แถวเปล่า
        }

        /* โหลด dropdown */
=======
    public function mount()
    {
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09
        $this->customers = CustomerModel::all();
        $this->customerDelivery = collect();
    }

<<<<<<< HEAD
    /* เติม state จากโมเดล (ใช้ได้ทั้ง mount & refresh) */
    private function fillFromModel(QuotationModel $q): void
    {
        $this->quotation_id = $q->id;
        $this->quote_number = $q->quote_number;
        $this->customer_id = $q->customer_id;
        $this->selected_delivery_id = $q->delivery_address_id;
        $this->quote_date = $q->quote_date?->toDateString() ?? now()->toDateString();
        $this->quote_note = $q->quote_note ?? '';
        $this->quote_enable_vat = $q->quote_enable_vat;
        $this->quote_vat_included = $q->quote_vat_included;
        $this->quote_discount = $q->quote_discount;
        $this->quote_status = $q->quote_status?->value ?? 'wait';
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
                    'product_detail' => $i->product_detail ?? ($product ? $product->product_size : ''), // (ถ้าเก็บใน DB)
                    'product_length' => $i->product_length,
                    'product_weight' => $i->product_weight,
                    'product_calculation' => $i->product_calculation ?? 1,
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
            // อนุญาตให้ items เป็น array ว่างได้
            'items' => 'nullable|array',
            // แต่ถ้า items ไม่ว่าง (มีอย่างน้อยหนึ่งแถว) จะเช็คต่อที่ product_id/quantity/price
            'items.*.product_id' => 'required_with:items|exists:products,product_id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ];
    }

    public function updated($name, $value)
    {
        // ตรวจสอบ field ว่าเป็น product_search
        if (preg_match('/items\.(\d+)\.product_search/', $name, $matches)) {
            $index = $matches[1];

            $this->items[$index]['product_results'] = ProductModel::where('product_name', 'like', "%{$value}%")
                ->take(10)
                ->get(['product_id', 'product_name', 'product_size'])
                ->toArray();
            $this->items[$index]['product_results_visible'] = true;
        }
    }

    public function selectProduct($index, $productId, $productName)
    {
        $this->items[$index]['product_id'] = $productId;
        $this->items[$index]['product_search'] = $productName;
        $this->items[$index]['product_results'] = [];
        $this->items[$index]['product_size'] = [];
        $this->items[$index]['product_results_visible'] = false;
        $this->updatedItems($productId, "items.{$index}.product_id");
    }

    public function save()
    {
        $this->calculateTotals();
        $this->validate(); // กรณีลบ items ทั้งหมด จะไม่ error แล้ว (ตามข้อ 1)

        DB::transaction(function () {
            $isCreate = !$this->quotation_id;

            // 1) Running number ถ้า create
            if ($isCreate) {
                $this->quote_number = $this->generateRunningNumber();
            }

            // 2) Upsert Quotation
            $q = QuotationModel::updateOrCreate(
                ['id' => $this->quotation_id],
                [
                    'quote_number' => $this->quote_number,
                    'customer_id' => $this->customer_id,
                    'delivery_address_id' => $this->selected_delivery_id,
                    'quote_date' => $this->quote_date,
                    'quote_note' => $this->quote_note,
                    'quote_subtotal' => $this->quote_subtotal,
                    'quote_vat' => $this->quote_vat,
                    'quote_grand_total' => $this->quote_grand_total,
                    'quote_enable_vat' => $this->quote_enable_vat,
                    'quote_discount' => $this->quote_discount,
                    // 'product_calculation'  => $this->product_calculation,
                    'quote_vat_included' => $this->quote_vat_included,
                    'quote_status' => $this->quote_status,
                    'created_by' => $isCreate ? Auth::id() : $this->quotation->created_by ?? Auth::id(),
                    'updated_by' => Auth::id(),
                ],
            );

            // 3) Sync Items
            $existingIds = [];
            foreach ($this->items as $row) {
                // ข้ามแถวที่ไม่มี product_id (กรณี user ลบ items หรือเหลือแค่แถวว่าง)
                if (empty($row['product_id'])) {
                    continue;
                }

                // updateOrCreate ตาม ID (ถ้ามี) หรือสร้างใหม่
                $item = $q->items()->updateOrCreate(
                    ['id' => $row['id'] ?? null],
                    [
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_type' => $row['product_type'],
                        'product_unit' => $row['product_unit'],
                        'product_detail' => $row['product_detail'] ?? null,
                        'product_length' => $row['product_length'],
                        'product_weight' => $row['product_weight'],
                        'product_calculation' => $row['product_calculation'],
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'total' => $row['quantity'] * $row['unit_price'],
                    ],
                );
                $existingIds[] = $item->id;
            }

            // ถ้า user “ลบทิ้งหมด” $existingIds จะว่างเปล่า → whereNotIn จะเป็น true ทั้งหมด → ลบ items ทั้งหมดใน DB
            $q->items()->whereNotIn('id', $existingIds)->delete();

            $this->quotation_id = $q->id;
        });

        $this->dispatch('notify', type: 'success', message: $this->quotation_id ? 'อัปเดตใบเสนอราคาเรียบร้อย' : 'สร้างใบเสนอราคาเรียบร้อย');

        // redirect หลัง save
        if (!$this->isCreate) {
            return redirect()->route('quotations.edit', $this->quotation_id);
        }

        return redirect()->route('quotations.index');
    }

    /* ─────────── Running Number : QTyyMM#### ─────────── */
    private function generateRunningNumber(): string
    {
        $prefix = 'QT' . now()->format('ym');
        $last = QuotationModel::where('quote_number', 'like', $prefix . '%')->max('quote_number');
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
            'product_calculation' => 1,
            'product_length' => null,
            'product_weight' => null,
            'product_detail' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function updatedItems($value, $key): void
    {
        [$index, $field] = explode('.', str_replace('items.', '', $key), 2);

        if ($field === 'product_id') {
            $product = ProductModel::find($value);
            if (!$product) {
                return;
            }

            // ─── ถ้าแถวนี้มี id เก่า (คือแถวแก้ไขจาก DB) ให้ bypass merge
            $currentId = $this->items[$index]['id'] ?? null;

            // ใช้ลูปเฉพาะแถวที่ "มี id เดิม" หรือ "เป็นแถวใหม่ที่ไม่มี id"
            foreach ($this->items as $i => $item) {
                // ข้ามแถวเดียวกัน และข้ามแถวที่เพิ่งมี id เดิม
                if ($i == $index) {
                    continue;
                }

                // ข้ามแถวที่มาจาก DB แล้ว (ถ้าเราต้องการ merge เฉพาะแถวใหม่กับแถวใหม่)
                // หรือ เลือกเงื่อนไขที่เหมาะกับกรณีของคุณ
                $otherId = $item['id'] ?? null;

                // ถ้าเราต้องการให้ "merge เฉพาะแถวใหม่กับแถวใหม่"
                // ให้เขียนแบบนี้:
                if (is_null($currentId) && is_null($otherId) && $item['product_id'] == $value) {
                    // merge แถวใหม่ทั้งสอง (quantity) แล้ว unset แถวนี้
                    $this->items[$i]['quantity'] += $this->items[$index]['quantity'] ?? 1;
                    unset($this->items[$index]);
                    $this->items = array_values($this->items);
                    $this->calculateTotals();
                    return;
                }

                //หากสินค้าซ้ำให้เพิ่มจำนวน
                if ($item['product_id'] == $value) {
                    $this->items[$i]['quantity'] += $this->items[$index]['quantity'] ?? 1;
                    unset($this->items[$index]);
                    $this->items = array_values($this->items);
                    $this->calculateTotals();
                    return;
                }
            }

            // ถ้าถึงตรงนี้ → ไม่มีแถวไหนซ้ำ ให้อัปเดตข้อมูลสินค้า
            $this->items[$index]['product_detail'] = $product->product_size;
            $this->items[$index]['product_type'] = $product->productType->value;
            $this->items[$index]['product_name'] = $product->product_name;
            $this->items[$index]['unit_price'] = $product->product_price;
            $this->items[$index]['product_weight'] = $product->product_weight;
            $this->items[$index]['product_unit'] = $product->productUnit->value;
            $this->items[$index]['product_length'] = $product->product_length ?? null;
        }

        // ─── คำนวณยอดรวมใหม่ทุกครั้ง
        foreach ($this->items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
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

    public function calculateTotals(): void
    {
        $this->quote_subtotal = collect($this->items)->sum(function ($i) {
            $quantity = (float) ($i['quantity'] ?? 0);
            $unitPrice = (float) ($i['unit_price'] ?? 0);
            $length = (float) ($i['product_length'] ?? 1);
            $thickness = (float) ($i['product_calculation'] ?? 1);

            if ($length <= 0) {
                $length = 1;
            }
            if ($thickness <= 0) {
                $thickness = 1;
            }

            return $quantity * $unitPrice * $length * $thickness;
        });

        // ✅ ใช้ quote_subtotal ในการคำนวณ
        $netSubtotal = max(0, $this->quote_subtotal - $this->quote_discount);

        if (!$this->quote_enable_vat) {
            $this->quote_vat = 0;
            $this->quote_grand_total = $netSubtotal;
            return;
        }

        if ($this->quote_vat_included) {
            $net = round($netSubtotal / 1.07, 2);
            $this->quote_vat = round($net * 0.07, 2);
            $this->quote_subtotal = $net;
            $this->quote_grand_total = $net + $this->quote_vat;
        } else {
            $this->quote_vat = round($netSubtotal * 0.07, 2);
            $this->quote_grand_total = $netSubtotal + $this->quote_vat;
        }
    }

    public function updatedQuoteDiscount()
    {
        $this->calculateTotals();
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
=======
#[On('customer-created-success')]
public function handleCustomerCreatedSuccess(array $payload)
{
    $this->customer_id = (int) ($payload['customerId'] ?? 0);
    $this->refreshCustomers(); // ✅ reuse method ที่โหลดข้อมูลใหม่จริง
}

public function reloadCustomerListAndSelect($customerId)
{
    $this->customers = CustomerModel::all();
    $this->customer_id = (int) $customerId;
    $this->updatedCustomerId($this->customer_id);
}

    public function updatedCustomerId($value)
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09
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

    public function render()
    {
        return view('livewire.quotations.quotations-form')->layout('layouts.horizontal', ['title' => 'Quotations']);
    }

    /// convert Quotations To orers

    public function approveQuotation($quotationId)
    {
        // 1) โหลดใบเสนอราคา ใหม่อีกครั้ง
        $quotation = QuotationModel::with(['items', 'customer', 'deliveryAddress', 'sale'])->find($quotationId);

        if (!$quotation) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'ไม่พบใบเสนอราคา',
            ]);
            return;
        }

        // 2) ตรวจสถานะก่อน (ต้องเป็น 'wait')
        if ($quotation->quote_status !== 'wait') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'ใบเสนอราคานี้ไม่สามารถอนุมัติได้อีก',
            ]);
            return;
        }

        // 3) เริ่ม transaction เพื่อ clone ไปเป็น Order
        DB::transaction(function () use ($quotation) {
            // 3.1) สร้าง Running Number สำหรับ Order
            $prefix = 'OR' . now()->format('ym');
            $lastOrderNumber = OrderModel::where('order_number', 'like', $prefix . '%')
                ->orderBy('order_number', 'desc')
                ->value('order_number');
            $next = $lastOrderNumber ? ((int) substr($lastOrderNumber, -4)) + 1 : 1;
            $orderNumber = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            // 3.2) สร้าง Order หลัก (clone header จาก Quotation)
            $order = OrderModel::create([
                'quote_id' => $quotation->id,
                'order_number' => $orderNumber,
                'order_date' => now()->toDateString(),
                'customer_id' => $quotation->customer_id,
                'delivery_address_id' => $quotation->delivery_address_id,
                'order_subtotal' => $quotation->quote_subtotal,
                'order_discount' => $quotation->quote_discount,
                'order_vat' => $quotation->quote_vat,
                'order_grand_total' => $quotation->quote_grand_total,
                'order_payment_status' => 'pending',
                'order_status' => 'open',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // 3.3) Clone รายการสินค้า (QuotationItem → OrderItems)
            foreach ($quotation->items as $qItem) {
                OrderItemsModel::create([
                    'order_id' => $order->id,
                    'product_id' => $qItem->product_id,
                    'product_name' => $qItem->product_name,
                    'product_type' => $qItem->product_type,
                    'product_unit' => $qItem->product_unit,
                    'product_detail' => $qItem->product_detail,
                    'product_length' => $qItem->product_length,
                    'product_weight' => $qItem->product_weight,
                    'product_calculation' => $qItem->product_calculation,
                    'quantity' => $qItem->quantity,
                    'unit_price' => $qItem->unit_price,
                    'total' => $qItem->total,
                ]);
            }

            // 3.4) เปลี่ยนสถานะ Quotation → 'approved'
            $quotation->update(['quote_status' => 'success']);

            // 3.5) แจ้ง success แล้ว redirect ไปหน้า Order ใหม่
            $this->dispatch('notify', type: 'success', message: 'สร้าง Order เรียบร้อย เลขที่: ' . $orderNumber );

         
            redirect()->route('orders.show', $order->id);
        });
    }
}
