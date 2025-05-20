<?php

namespace App\Livewire\Products;

use App\Models\products\ProductModel;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
   use WithPagination;

    // --- ฟิลด์สำหรับฟอร์ม ---
    public $product_id ,$product_code,$product_name,$product_weight,
           $product_price,$product_type,$product_unit,$product_note,
           $product_status = 1;

    public $search = '';
    public $perPage = 10;
    public $isEdit = false;

    protected $rules = [
        'product_code'   => 'required|string|max:50|unique:products,product_code',
        'product_name'   => 'required|string|max:255',
        'product_weight' => 'required|numeric|min:0',
        'product_price'  => 'required|numeric|min:0',
        'product_type'   => 'required|string|max:100',
        'product_unit'   => 'required|string|max:50',
        'product_note'   => 'nullable|string',
        'product_status' => 'boolean'
    ];

    // -- รีเซ็ตค่าเมื่อปิด modal
    public function resetForm()
    {
        $this->reset([
            'product_id','product_code','product_name','product_weight',
            'product_price','product_type','product_unit','product_note',
            'product_status','isEdit'
        ]);
        $this->resetValidation();
    }

    // -- สร้างข้อมูล
    public function store()
    {
        $this->validate();

        ProductModel::create($this->only([
            'product_code','product_name','product_weight','product_price',
            'product_type','product_unit','product_note','product_status'
        ]));

        $this->dispatch('close-modal');       // Alpine event
        $this->resetForm();
        $this->dispatch('notify', text:'บันทึกข้อมูลสำเร็จ');
    }

    // -- โหลดข้อมูลเพื่อแก้ไข
    public function edit($id)
    {
        $product = ProductModel::findOrFail($id);
        $this->fill($product->toArray());
        $this->isEdit = true;
        $this->dispatch('open-modal');
    }

    // -- อัปเดต
    public function update()
    {
        $rules = $this->rules;
        $rules['product_code'] .= ',' . $this->product_id . ',product_id'; // ยกเว้น record ปัจจุบัน
        $this->validate($rules);

        $product = ProductModel::findOrFail($this->product_id);
        $product->update($this->only([
            'product_code','product_name','product_weight','product_price',
            'product_type','product_unit','product_note','product_status'
        ]));

        $this->dispatch('close-modal');
        $this->resetForm();
        $this->dispatch('notify', text:'แก้ไขข้อมูลสำเร็จ');
    }

    // -- ลบ
    public function destroy($id)
    {
        ProductModel::findOrFail($id)->delete();
        $this->dispatch('notify', text:'ลบข้อมูลแล้ว');
    }
 public function updatingSearch(): void
    {
        $this->resetPage();
    }

 public function render()
    {
        $keyword = trim($this->search);          // เล็มช่องว่างหัว-ท้าย

        $products = ProductModel::query()
            ->when($keyword !== '', function ($q) use ($keyword) {   // ← ★ use ($keyword)
                $q->where(function ($sub) use ($keyword) {          // ← ★ use ($keyword) อีกชั้น
                    $sub->where('product_name',  'like', "%{$keyword}%")
                        ->orWhere('product_code', 'like', "%{$keyword}%")
                        ->orWhere('product_id',   'like', "%{$keyword}%");
                });
            })
            ->latest('product_id')
            ->paginate($this->perPage);

        return view('livewire.products.product-index', compact('products'))
               ->layout('layouts.horizontal', ['title' => 'Products']);
    }

}
