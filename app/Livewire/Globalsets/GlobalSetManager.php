<?php

namespace App\Livewire\Globalsets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\globalsets\GlobalSetModel;

class GlobalSetManager extends Component
{
    use WithPagination;

    /* ────────── ฟิลด์ฟอร์ม ────────── */
    public $name        = '';
    public $description = '';
    public $values      = [];          // array [['value'=>'','status'=>'Enable'], …]
    public $editingId   = null;

    /* กฎ Validate */
    protected $rules = [
        'name'                 => 'required|string|max:255',
        'description'          => 'nullable|string',
        'values.*.value'       => 'required|string|max:255',
        'values.*.status'      => 'required|in:Enable,Disable',
    ];

    /* ---------- เมธอด CRUD ---------- */
    public function create(): void
    {
        $this->reset(['name','description','values','editingId']);
        $this->values = [['value'=>'','status'=>'Enable']];
        $this->dispatch('open-modal');
    }

    public function edit(int $id): void
    {
        $set              = GlobalSetModel::with('values')->findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $set->name;
        $this->description = $set->description;
        $this->values      = $set->values
                                 ->map(fn ($v) => ['value'=>$v->value,'status'=>$v->status])
                                 ->toArray();

        $this->dispatch('open-modal');
    }

    public function addValue(): void
    {
        $this->values[] = ['value' => '', 'status' => 'Enable'];
    }

    public function removeValue(int $index): void
    {
        unset($this->values[$index]);
        $this->values = array_values($this->values);
    }

    public function save(): void
    {
        $this->validate();

        $set = GlobalSetModel::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'description' => $this->description]
        );

        $set->values()->delete();                // เคลียร์ค่าก่อน
        foreach ($this->values as $i => $item) {
            $set->values()->create([
                'value'      => $item['value'],
                'status'     => $item['status'],
                'sort_order' => $i,
            ]);
        }

        $this->resetPage();                      // กลับหน้า 1 ถ้าจำเป็น
        $this->dispatch('close-modal');
        $this->dispatch('notify', text:'บันทึกข้อมูลสำเร็จ');
    }

    public function delete(int $id): void
    {
        GlobalSetModel::findOrFail($id)->delete();
        $this->resetPage();
        $this->dispatch('notify', text:'ลบข้อมูลแล้ว');
    }

    /* ---------- Render ---------- */
    public function render()
    {
        /**  ❌ อย่าเก็บไว้ใน property  */
        $globalSets = GlobalSetModel::withCount('values')
                        ->latest()
                        ->paginate(10);

        return view('livewire.globalsets.global-set-manager', compact('globalSets'))
               ->layout('layouts.horizontal', ['title' => 'Global Sets']);
    }
}
