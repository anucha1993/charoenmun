<div>
    {{-- ───── Toast + Modal Controller ───── --}}
    <script>
        /* toastr */
        window.addEventListener('notify', e => {
            const { type='success', text='' } = e.detail
            toastr.options = { timeOut: 3500, progressBar: true, positionClass: 'toast-top-right' }
            toastr[type](text)
        })

        /* bootstrap modal */
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('global-set-modal'))
            window.addEventListener('open-modal',  () => modal.show())
            window.addEventListener('close-modal', () => modal.hide())
        })
    </script>
<div class="container-fluid">
    {{-- ───── Page Header ───── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 page-title-box ">
        <h3 class="page-title">Global Sets</h3>
        <button class="btn btn-primary" wire:click="create">
            <i class="ri-add-line me-1"></i> เพิ่ม
        </button>
    </div>
   

    {{-- ───── Card & Table ───── --}}
    <div class="card shadow-sm  ">
        <div class="card-header"><h3 class="mb-0 ">รายการ Global Sets</h3></div>

        <div class="card-body p-0">
            <table class="table table-centered mb-0" id="inline-editable" style="font-size: 18px">
                <thead>
                    <tr>
                        <th style="width:70px;">IDs</th>
                        <th>ชื่อ</th>
                        <th>คำอธิบาย</th>
                        <th class="text-center">จำนวนค่า</th>
                        <th class="text-center" style="width:140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($globalSets as $set)
                        <tr>
                            <td>{{ $set->id }}</td>
                            <td>{{ $set->name }}</td>
                            <td>{{ $set->description }}</td>
                            <td class="text-center">{{ $set->values_count }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1" wire:click="edit({{ $set->id }})">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        onclick="confirm('ยืนยันลบ?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $set->id }})">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">— ไม่มีข้อมูล —</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $globalSets->links() }}   {{-- ใช้ view bootstrap-5 อัตโนมัติถ้า set ใน AppServiceProvider --}}
        </div>
    </div>

    {{-- ───── Modal ───── --}}
    <div wire:ignore.self class="modal fade" id="global-set-modal" tabindex="-1" aria-labelledby="globalSetLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="save">   {{-- ← ใช้เมธอดเดียว save() --}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalSetLabel">
                            {{ $editingId ? 'แก้ไข Global Set' : 'เพิ่ม Global Set' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ *</label>
                                <input wire:model.defer="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">คำอธิบาย</label>
                                <input wire:model.defer="description" class="form-control @error('description') is-invalid @enderror">
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Global Set Values</label>
                                @foreach ($values as $idx => $item)
                                    <div class="row g-2 align-items-center mb-2">
                                        <div class="col">
                                            <input wire:model="values.{{ $idx }}.value" class="form-control" placeholder="ค่า">
                                        </div>
                                        <div class="col-md-3">
                                            <select wire:model="values.{{ $idx }}.status" class="form-control">
                                                <option value="Enable">Enable</option>
                                                <option value="Disable">Disable</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" wire:click="removeValue({{ $idx }})" class="btn btn-sm btn-danger">ลบ</button>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addValue" class="btn btn-sm btn-secondary mt-2">+ เพิ่มค่า</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                        <button class="btn btn-success" type="submit">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
