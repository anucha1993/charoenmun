<?php

namespace App\Livewire\Quotations;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use App\Enums\QuotationStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Orders\OrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\products\ProductModel;
use App\Models\Orders\OrderItemsModel;
use App\Models\customers\customerModel;
use App\Models\Quotations\QuotationModel;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Support\Facades\Storage;

/**
 * Livewire Component : สร้าง / แก้ไข ใบเสนอราคา
 */
#[Layout('layouts.horizontal', ['title' => 'Quotations'])]
class QuotationsForm extends Component
{
    /* ──────────────── State หลัก ──────────────── */
    public Collection $customers;
    public Collection $customerDelivery;
    public bool $isProcessing = false;
    public bool $spinner = false;
    public array $steps = [];
    public ?string $orderNumber = null;

    public ?int $customer_id = null;

    public ?int $selected_delivery_id = null;

    public ?customerModel $selectedCustomer = null;
    public ?deliveryAddressModel $selectedDelivery = null;

    public array $items = [];

    public float $quote_subtotal = 0;
    public float $quote_vat = 0;
    public float $quote_discount = 0;
    public float $quote_grand_total = 0;
    public array $searchResults = [];
    public float $netSubtotal = 0;

    public bool $quote_enable_vat = false;
    public bool $quote_vat_included = false;
    // public bool $checkVat = false;

    /* ─── ฟิลด์ใบเสนอราคา ─── */
    public ?int $quotation_id = null;
    public string $quote_number = ''; // รันอัตโนมัติเมื่อ create
    public string $quote_date; // default = วันนี้
    public string $quote_note = '';
    public string $quote_status = 'wait';

    /* ตัวแปรเก็บโมเดล (ถ้ามี) */
    public ?QuotationModel $quotation = null;

    public float $quote_subtotal_before_discount = 0;

    /* ─── ตัวแปรสำหรับระบบค้นหาและกรองสินค้า ─── */
    public string $global_product_search = '';
    public string $product_type_filter = '';
    public string $price_range_filter = '';
    public Collection $filtered_products;
    public bool $quote_request_print_format = false;

    public function mount(?int $id = null): void
    {
        /* default: วันนี้ */
        $this->quote_date = now()->toDateString();
        
        /* ─── Initialize ตัวแปรค้นหาและกรอง ─── */
        $this->filtered_products = collect();
        
        /* 1) โหมดแก้ไข --------------------------------------------------- */
        if ($id) {
            $this->quotation_id = $id;
            $quotation = QuotationModel::with(['items', 'customer', 'deliveryAddress', 'sale'])->find($id);

            if ($quotation) {
                $this->quote_number = $quotation->quote_number;
                //$this->quote_status = $quotation->quote_status;
                $this->fillFromModel($this->quotation);
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

        $this->customers = customerModel::orderBy('created_at', 'desc')->get();
        $this->customerDelivery = $this->customer_id ? deliveryAddressModel::where('customer_id', $this->customer_id)->get() : collect();
        $this->calculateTotals(); // <-- เพิ่มตรงนี้
    }

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
        $this->quote_status = $q->quote_status->value;
        $this->selectedCustomer = customerModel::find($q->customer_id);
        $this->selectedDelivery = deliveryAddressModel::find($q->delivery_address_id);

        /* map items -> array */
        $this->items = $q->items
            ->map(function ($i) {
                $product = ProductModel::find($i->product_id);
                return [
                    'id' => $i->id,
                    'product_id' => $i->product_id,
                    'product_name' => $i->product_name,
                    'product_measure' => $i->product_measure,
                    'product_note' => $i->product_note,
                    'product_type' => $i->product_type,
                    'product_unit' => $i->product_unit,
                    'product_detail' => $i->product_detail ?? ($product ? $product->product_size : ''), // (ถ้าเก็บใน DB)
                    'product_length' => $i->product_length,
                    'product_weight' => $i->product_weight,
                    'product_calculation' => $i->product_calculation ?? 1,
                    'quantity' => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'total' => $i->total ?? $i->quantity * $i->unit_price,
                    // 'checkVat' => $i->product_vat ?? false,
                    'product_vat' => (bool) ($i->product_vat ?? false),

                    // ✅ เพิ่มบรรทัดนี้:
                    'product_search' => $i->product_name . ' ' . ($product->productWireType?->value ?? ''), // แสดงชื่อใน input
                    'product_results' => [],
                    'product_results_visible' => false,
                    'selected_from_dropdown' => true, // ✅ ข้อมูลจาก DB ถือว่าเป็นการเลือกที่ถูกต้อง
                ];
            })
            ->toArray();

        $this->calculateTotals(); // <-- เพิ่มตรงนี้
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
            'items.*.product_vat' => 'boolean',
        ];
    }

    /**
     * Validate product selection - ป้องกันการเพิ่มสินค้าเองโดยไม่เลือกจาก dropdown
     */
    protected function validateProductSelection(): bool
    {
        $hasError = false;
        
        foreach ($this->items as $index => $item) {
            // ข้ามแถวที่ว่างเปล่า
            if (empty($item['product_search']) && empty($item['product_id'])) {
                continue;
            }
            
            // ✅ หลักการใหม่: ถ้ามีข้อความในช่องค้นหา แต่ไม่มี product_id หรือไม่ได้เลือกจาก dropdown
            if (!empty($item['product_search']) && (
                empty($item['product_id']) || 
                empty($item['selected_from_dropdown'])
            )) {
                // ตรวจสอบว่าข้อความที่พิมพ์ตรงกับสินค้าที่มีในระบบหรือไม่
                $exactMatch = ProductModel::where('product_name', 'like', "%{$item['product_search']}%")->first();
                
                if (!$exactMatch) {
                    $this->addError("items.{$index}.product_search", 'ไม่พบสินค้าที่ค้นหา กรุณาเลือกสินค้าจากรายการที่แสดง');
                    $hasError = true;
                } else if (empty($item['product_id'])) {
                    $this->addError("items.{$index}.product_search", 'กรุณาเลือกสินค้าจากรายการที่แสดง');
                    $hasError = true;
                }
                continue;
            }
            
            // ถ้ามี product_id ให้ตรวจสอบว่าสินค้าจริงหรือไม่
            if (!empty($item['product_id'])) {
                $product = ProductModel::find($item['product_id']);
                if (!$product) {
                    $this->addError("items.{$index}.product_id", 'สินค้าที่เลือกไม่ถูกต้องหรือไม่มีอยู่ในระบบ');
                    $hasError = true;
                }
            }
        }
        
        return !$hasError;
    }

    public function updated($name, $value)
    {
        // ตรวจสอบ field ว่าเป็น product_search
        if (preg_match('/items\.(\d+)\.(product_search|unit_price|quantity|product_calculation|product_length|product_vat)/', $name, $matches)) {
            $this->calculateTotals();
        }
        // ตรวจสอบ field ว่าเป็น product_search
        if (preg_match('/items\.(\d+)\.product_search/', $name, $matches)) {
            $index = $matches[1];

            // ✅ อนุญาตให้ค้นหาได้ปกติ - รีเซ็ต product_id เมื่อเริ่มพิมพ์ใหม่
            if (!empty($value)) {
                // เคลียร์ product_id เมื่อเริ่มพิมพ์ใหม่ (เพื่อให้ต้องเลือกจาก dropdown)
                if (isset($this->items[$index]['product_id']) && !empty($this->items[$index]['product_id'])) {
                    $this->items[$index]['selected_from_dropdown'] = false;
                }

                // ค้นหาสินค้าตาม input
                $results = ProductModel::with('productWireType:id,value')
                    ->where('product_name', 'like', "%{$value}%")
                    ->take(10)
                    ->get(['product_id', 'product_name', 'product_size', 'product_wire_type']);
                
                $this->items[$index]['product_results'] = $results;
                $this->items[$index]['product_results_visible'] = $results->isNotEmpty();
            } else {
                // ถ้าลบข้อความหมด ให้เคลียร์ dropdown
                $this->items[$index]['product_results'] = [];
                $this->items[$index]['product_results_visible'] = false;
            }
        }
    }

    public function selectProduct($index, $productId, $productName)
    {
        $product = ProductModel::with('productWireType:id,value')->find($productId);

        $this->items[$index]['product_id'] = $productId;
        $this->items[$index]['product_search'] = $productName . ' ' . $product->productWireType?->value;
        $this->items[$index]['product_size'] = $product->product_size;
        $this->items[$index]['product_wire_type'] = $product->productWireType?->value ?? null;

        // ✅ เพิ่มการบันทึกว่าเป็นการเลือกจาก dropdown (ไม่ใช่พิมพ์เอง)
        $this->items[$index]['selected_from_dropdown'] = true;
        
        $this->items[$index]['product_results'] = [];
        $this->items[$index]['product_results_visible'] = false;

        $this->updatedItems($productId, "items.{$index}.product_id");
    }

    /**
     * ✅ Method เพื่อ clear product selection
     */
    public function clearProductSelection($index)
    {
        $this->items[$index]['product_id'] = null;
        $this->items[$index]['product_search'] = '';
        $this->items[$index]['product_name'] = '';
        $this->items[$index]['product_type'] = '';
        $this->items[$index]['product_unit'] = '';
        $this->items[$index]['product_detail'] = null;
        $this->items[$index]['product_length'] = null;
        $this->items[$index]['product_weight'] = null;
        $this->items[$index]['unit_price'] = 0;
        $this->items[$index]['selected_from_dropdown'] = false;
        $this->items[$index]['product_results'] = [];
        $this->items[$index]['product_results_visible'] = false;
        
        $this->calculateTotals();
    }

    /**
     * ✅ Method สำหรับเพิ่มสินค้าจากระบบค้นหาแบบรวดเร็ว
     */
    public function addProductFromSearch($productId)
    {
        $product = ProductModel::with('productWireType:id,value')->find($productId);
        if (!$product) {
            return;
        }

        // เพิ่มสินค้าในแถวใหม่ (ไม่ merge กับแถวเดิม)
        $newItem = [
            'product_id' => $productId,
            'product_name' => $product->product_name,
            'product_search' => $product->product_name . ' ' . ($product->productWireType?->value ?? ''),
            'product_type' => $product->productType->value,
            'product_unit' => $product->productUnit->value,
            'product_note' => $product->product_note,
            'product_calculation' => $product->product_calculation,
            'product_length' => $product->product_length ?? null,
            'product_weight' => $product->product_weight,
            'product_detail' => $product->product_size,
            'product_measure' => $product->product_measure,
            'quantity' => 1,
            'unit_price' => $product->product_price,
            'total' => $product->product_price,
            'product_vat' => false,
            'product_results' => collect(),
            'product_results_visible' => false,
            'selected_from_dropdown' => true,
        ];

        $this->items[] = $newItem;
        $this->calculateTotals();
    }

    /**
     * ✅ Method สำหรับเคลียร์ตัวกรองสินค้า
     */
    public function clearProductFilters()
    {
        $this->global_product_search = '';
        $this->product_type_filter = '';
        $this->price_range_filter = '';
        $this->filtered_products = collect();
    }

    /**
     * ✅ Watchers สำหรับระบบค้นหาและกรอง
     */
    public function updatedGlobalProductSearch()
    {
        $this->updateFilteredProducts();
    }

    public function updatedProductTypeFilter()
    {
        $this->updateFilteredProducts();
    }

    public function updatedPriceRangeFilter()
    {
        $this->updateFilteredProducts();
    }

    /**
     * ✅ Method สำหรับอัปเดตผลการค้นหาที่กรองแล้ว
     */
    private function updateFilteredProducts()
    {
        if (empty($this->global_product_search) && empty($this->product_type_filter) && empty($this->price_range_filter)) {
            $this->filtered_products = collect();
            return;
        }

        $query = ProductModel::with(['productType', 'productUnit']);

        // กรองตามคำค้นหา
        if (!empty($this->global_product_search)) {
            $query->where(function ($q) {
                $searchTerm = $this->global_product_search;
                $q->where('product_name', 'like', "%{$searchTerm}%")
                  ->orWhere('product_size', 'like', "%{$searchTerm}%")
                  ->orWhere('product_id', 'like', "%{$searchTerm}%");
            });
        }

        // กรองตามประเภทสินค้า
        if (!empty($this->product_type_filter)) {
            $query->whereHas('productType', function ($q) {
                $q->where('value', 'like', "%{$this->product_type_filter}%");
            });
        }

        // กรองตามช่วงราคา
        if (!empty($this->price_range_filter)) {
            switch ($this->price_range_filter) {
                case '0-1000':
                    $query->whereBetween('product_price', [0, 1000]);
                    break;
                case '1000-5000':
                    $query->whereBetween('product_price', [1000, 5000]);
                    break;
                case '5000-10000':
                    $query->whereBetween('product_price', [5000, 10000]);
                    break;
                case '10000+':
                    $query->where('product_price', '>', 10000);
                    break;
            }
        }

        $this->filtered_products = $query->limit(20)->get();
    }

    public function save()
    {
        // $this->calculateTotals();
        $this->refreshVat();
        
        // ✅ เพิ่ม validation สำหรับป้องกันการเพิ่มสินค้าเอง
        if (!$this->validateProductSelection()) {
            $this->dispatch('notify', type: 'error', message: 'กรุณาเลือกสินค้าจากรายการที่กำหนดเท่านั้น');
            return;
        }
        
        $this->validate(); // กรณีลบ items ทั้งหมด จะไม่ error แล้ว (ตามข้อ 1)
        logger('after refresh', $this->items);
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
                        'product_measure' => $row['product_measure'],
                        'product_note' => $row['product_note'],
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'total' => $this->calculateItemTotal($row),
                        'product_vat' => $row['product_vat'] ? 1 : 0,
                    ],
                );
                $existingIds[] = $item->id;
            }
            //logger('after refresh', $this->items);

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

    /**
     * Helper method to calculate item total consistently across the application
     */
    private function calculateItemTotal(array $item): float
    {
        $quantity = (float)($item['quantity'] ?? 0);
        $unitPrice = (float)($item['unit_price'] ?? 0);
        $calculation = (float)($item['product_calculation'] ?? 1);
        $length = (float)($item['product_length'] ?? 1);
        
        // คูณด้วยความยาวเสมอ ไม่ว่า product_calculation จะเป็นเท่าไร
        return $unitPrice * $calculation * $length * $quantity;
    }

    /**
     * ฟังก์ชันทดสอบการคำนวณ - เรียกใช้เพื่อตรวจสอบผลลัพธ์
     */
    public function testCalculation(): void
    {
        // สร้างข้อมูลทดสอบตามตัวอย่าง
        $testItem = [
            'product_id' => 1,
            'product_name' => 'แผ่นพื้นสำเร็จรูป ลาด 4 เส้น',
            'quantity' => 5,
            'unit_price' => 250,
            'product_calculation' => 0.35,
            'product_length' => 2,
            'total' => 0
        ];

        // คำนวณและพิมพ์ผลลัพธ์
        $total = $this->calculateItemTotal($testItem);
        
        // แสดงผลในหน้าเพจโดยใช้ notify
        $message = "การคำนวณทดสอบ: {$testItem['unit_price']} x {$testItem['product_calculation']} x {$testItem['product_length']} x {$testItem['quantity']} = {$total}";
        $this->dispatch('notify', type: 'info', message: $message);
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
            'product_note' => '',
            'product_calculation' => 1,
            'product_length' => null,
            'product_weight' => null,
            'product_detail' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
            'product_vat' => false,
            'product_results' => collect(),
            'product_results_visible' => false,
            'product_search' => '', // ✅ เพิ่ม field สำหรับ search
            'selected_from_dropdown' => false, // ✅ เพิ่ม flag สำหรับตรวจสอบการเลือก
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

            // ✅ ปรับเปลี่ยน: ไม่ merge สินค้าซ้ำ อนุญาตให้เพิ่มสินค้าซ้ำได้
            // ลบการตรวจสอบและ merge สินค้าซ้ำออก
            
            // อัปเดตข้อมูลสินค้าให้แถวนี้โดยตรง
            $this->items[$index]['product_detail'] = $product->product_size;
            $this->items[$index]['product_type'] = $product->productType->value;
            $this->items[$index]['product_name'] = $product->product_name;
            $this->items[$index]['product_note'] = $product->product_note;
            $this->items[$index]['unit_price'] = $product->product_price;
            $this->items[$index]['product_weight'] = $product->product_weight;
            $this->items[$index]['product_calculation'] = $product->product_calculation;
            $this->items[$index]['product_unit'] = $product->productUnit->value;
            $this->items[$index]['product_length'] = $product->product_length ?? null;
            $this->items[$index]['product_measure'] = $product->product_measure ?? null;
        }

        // ─── คำนวณยอดรวมใหม่ทุกครั้ง
        foreach ($this->items as &$item) {
            $item['total'] = $this->calculateItemTotal($item);
        }

        $this->calculateTotals();
    }

    public function openDeliveryModal(int $customer_id)
    {
        $this->dispatch('open-delivery-modal', customerId: $customer_id);
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
        // 1) คำนวณยอดรวมก่อน VAT (ทั้งสินค้ารวมกัน)
        $subtotal = collect($this->items)->sum(function ($i) {
            return $this->calculateItemTotal($i);
        });
        $this->quote_subtotal_before_discount = $subtotal;

        // 2) หักส่วนลด
        $netSubtotal = max(0, $subtotal - $this->quote_discount);

        // 3) ถ้าไม่เปิด VAT → เสร็จ
        if (!$this->quote_enable_vat) {
            $this->quote_subtotal = $netSubtotal;
            $this->quote_vat = 0;
            $this->quote_grand_total = $netSubtotal;
            return;
        }

        // 4) คำนวณยอดที่นำมาคิด VAT (เฉพาะรายการที่ product_vat == true)
        $vatItems = collect($this->items)
            ->filter(fn($i) => !empty($i['product_vat']))
            ->map(function ($i) {
                return $this->calculateItemTotal($i);
            });
        $vatableBase = $vatItems->sum();

        // 4.1 เฉลี่ยส่วนลดไปยังสินค้าที่มี VAT ตามสัดส่วน
        $vatDiscount = 0;
        $vatableBaseAfterDiscount = $vatableBase;
        if ($subtotal > 0 && $this->quote_discount > 0 && $vatableBase > 0) {
            // ใช้ round() เปรียบเทียบ float เพื่อความแม่นยำ
            if (round($vatableBase, 2) == round($subtotal, 2)) {
                $vatDiscount = $this->quote_discount;
                $vatableBaseAfterDiscount = max(0, $subtotal - $this->quote_discount);
            } else {
                $vatDiscount = $this->quote_discount * ($vatableBase / $subtotal);
                $vatableBaseAfterDiscount = max(0, $vatableBase - $vatDiscount);
            }
        }

        // 5) ถ้า VAT รวมในราคา (VAT-In)
        if ($this->quote_vat_included) {
            $netVatable = round($vatableBaseAfterDiscount / 1.07, 2);
            $vatAmount = round($vatableBaseAfterDiscount - $netVatable, 2);
            $this->quote_subtotal = round($netSubtotal - $vatAmount, 2);
            $this->quote_vat = $vatAmount;
            $this->quote_grand_total = round($this->quote_subtotal + $vatAmount, 2);
        } else {
            $vatAmount = round($vatableBaseAfterDiscount * 0.07, 2);
            $this->quote_subtotal = $netSubtotal;
            $this->quote_vat = $vatAmount;
            $this->quote_grand_total = round($netSubtotal + $vatAmount, 2);
        }
    }

    /* เรียกเมื่อผู้ใช้เปลี่ยนส่วนลด */
    public function updatedQuoteDiscount()
    {
        $this->calculateTotals();
    }

    /* เปิด-ปิด “คำนวณ VAT 7 %” */
    public function updatedQuoteEnableVat($value): void
    {
        $this->refreshVat();
        $this->calculateTotals();
    }

    /* เปิด-ปิด “VAT รวมในราคา” */
    public function updatedQuoteVatIncluded($value): void
    {
        $this->refreshVat();
        $this->calculateTotals();
    }

    /* ถูกเรียกจาก checkbox แต่ละแถว   wire:change="refreshVat" */
    public function refreshVat(): void
    {
        foreach ($this->items as $i => $row) {
            unset($this->items[$i]['checkVat']); // เคลียร์คีย์เก่าถ้ามี
            $this->items[$i]['product_vat'] = filter_var($row['product_vat'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }
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
        $this->selectedCustomer = customerModel::find($value);
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
        $this->refreshCustomers();
    }

    #[On('delivery-created-success')]
    public function handleDeliveryCreatedSuccess(array $payload): void
    {
        logger('Delivery created success received', $payload);
        
        $this->selected_delivery_id = $payload['deliveryId'] ?? null;

        // โหลด dropdown ใหม่ (เพื่อให้มี delivery option)
        $this->refreshCustomers(); // ✅ โหลด customerDelivery ใหม่

        // อัปเดต selectedDelivery ด้วย
        if ($this->selected_delivery_id) {
            $this->selectedDelivery = deliveryAddressModel::find($this->selected_delivery_id);
            logger('Selected delivery updated', ['delivery' => $this->selectedDelivery]);
        }
    }

   public function refreshCustomers(): void
{
    $this->customers = customerModel::orderBy('created_at', 'desc')->get();

    if ($this->customer_id) {
        $this->customerDelivery = deliveryAddressModel::where('customer_id', $this->customer_id)->get();

        // ✅ ไม่ต้องเรียก updatedCustomerId เพราะมันล้าง selected_delivery_id
        $this->selectedCustomer = customerModel::find($this->customer_id);
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
        return view('livewire.quotations.quotations-form', compact('products'));
    }

    /// convert Quotations To orers

    public function approveQuotation()
    {
        $this->dispatch('step-change', 'กำลังสร้าง PDF...');
        $quotationId = $this->quotation_id;

        $this->dispatch('step-change', 'กำลังโหลดใบเสนอราคา...');
        usleep(500 * 1000); // 0.5 วิ พอแค่ให้เห็น

        $quotation = QuotationModel::with(['items', 'customer', 'deliveryAddress', 'sale'])->find($quotationId);

        if (!$quotation || $quotation->quote_status !== QuotationStatus::Wait) {
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาดไม่สามารถสร้างคำสั่งซื้อซ้ำได้!');
            return;
        }

        DB::transaction(function () use ($quotation) {
            $this->dispatch('step-change', 'กำลังสร้างเลขที่ใบสั่งซื้อ...');
            usleep(500 * 1000);

            $prefix = 'OR' . now()->format('ym');
            $lastOrderNumber = OrderModel::where('order_number', 'like', $prefix . '%')
                ->orderBy('order_number', 'desc')
                ->value('order_number');
            $next = $lastOrderNumber ? ((int) substr($lastOrderNumber, -4)) + 1 : 1;
            $orderNumber = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            $this->dispatch('step-change', 'บันทึกใบสั่งซื้อ...');
            usleep(500 * 1000);

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
                    'product_vat' => $qItem->product_vat,
                    'product_note' => $qItem->product_note,
                    'product_calculation' => $qItem->product_calculation,
                    'product_measure' => $qItem->product_measure,
                    'quantity' => $qItem->quantity,
                    'unit_price' => $qItem->unit_price,
                    'total' => $qItem->total,
                ]);
            }

            $quotation->update(['quote_status' => 'success']);
            $quoteDate = Carbon::parse($quotation->quote_date);
            $year = $quoteDate->format('Y');
            $month = $quoteDate->format('m');

            // Export PDF
            // $this->dispatch('step-change', 'Export PDF...');
            // $pdf = Pdf::loadView('livewire.quotations.print', ['quotation' => $quotation]);
            // $pdf->setOptions([
            //     'isHtml5ParserEnabled' => true,
            //     'isRemoteEnabled' => true,
            //     'defaultFont' => 'THSarabunNew',
            // ]);
            // $filename = $quotation->quote_number . '.pdf';
            //  $path = "quotations/{$year}/{$month}/{$filename}";
            //  Storage::disk('public')->put($path, $pdf->output());

            $this->dispatch('notify', type: 'success', message: 'สร้าง Order สำเร็จ: ' . $orderNumber);

            redirect()->route('order.show', $order->id);
        });
    }

    /**
     * ไม่อนุมัติใบเสนอราคา พร้อมเหตุผล
     */
    public function rejectQuotation($quotationId, $rejectReason = null)
    {
        try {
            DB::beginTransaction();
            
            $quotation = QuotationModel::find($quotationId);
            
            if (!$quotation) {
                $this->dispatch('notify', type: 'error', message: 'ไม่พบใบเสนอราคา');
                return;
            }
            
            if ($quotation->quote_status !== QuotationStatus::Wait) {
                $this->dispatch('notify', type: 'error', message: 'สถานะใบเสนอราคาไม่ถูกต้อง');
                return;
            }
            
            // ตรวจสอบว่ามีเหตุผลหรือไม่
            if (empty($rejectReason) || trim($rejectReason) === '') {
                $this->dispatch('notify', type: 'warning', message: 'กรุณาระบุเหตุผลการไม่อนุมัติ');
                return;
            }
            
            // อัปเดตสถานะและบันทึกเหตุผล
            $quotation->update([
                'quote_status' => QuotationStatus::Cancel,
                'quote_note' => ($quotation->quote_note ? $quotation->quote_note . "\n\n" : '') . 
                               "❌ เหตุผลการไม่อนุมัติ: " . $rejectReason . 
                               "\n📅 วันที่: " . now()->format('d/m/Y H:i') . 
                               "\n👤 โดย: " . Auth::user()->name
            ]);
            
            DB::commit();
            
            $this->dispatch('notify', type: 'success', message: 'ไม่อนุมัติใบเสนอราคาเรียบร้อยแล้ว');
            
            // รีเฟรชข้อมูล
            $this->quotation = $quotation->fresh();
            $this->quote_status = $this->quotation->quote_status->value; // แปลง enum เป็น string
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * เปลี่ยนสถานะกลับเป็น รออนุมัติ (กรณีกดไม่อนุมัติผิด)
     */
    public function revertToWaitStatus($quotationId)
    {
        try {
            DB::beginTransaction();
            
            $quotation = QuotationModel::find($quotationId);
            
            if (!$quotation) {
                $this->dispatch('notify', type: 'error', message: 'ไม่พบใบเสนอราคา');
                return;
            }
            
            if ($quotation->quote_status !== QuotationStatus::Cancel) {
                $this->dispatch('notify', type: 'error', message: 'สามารถเปลี่ยนสถานะได้เฉพาะใบเสนอราคาที่ถูกยกเลิกเท่านั้น');
                return;
            }
            
            // เปลี่ยนสถานะกลับเป็น wait
            $quotation->update([
                'quote_status' => QuotationStatus::Wait,
                'quote_note' => ($quotation->quote_note ? $quotation->quote_note . "\n\n" : '') . 
                               "🔄 เปลี่ยนสถานะกลับเป็น รออนุมัติ" . 
                               "\n📅 วันที่: " . now()->format('d/m/Y H:i') . 
                               "\n👤 โดย: " . Auth::user()->name
            ]);
            
            DB::commit();
            
            $this->dispatch('notify', type: 'success', message: 'เปลี่ยนสถานะเป็น รออนุมัติ เรียบร้อยแล้ว');
            
            // รีเฟรชข้อมูล
            $this->quotation = $quotation->fresh();
            $this->quote_status = $this->quotation->quote_status->value;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
