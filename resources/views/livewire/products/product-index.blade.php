<div>
    <style>
        
    </style>

    <div class="content-page">
        <div class="content">

            <div class="col-12">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">

                            </div>
                            <h1 class="page-title" style="font-size: 25px">รายการสินค้าทั้งหมด</h1>
                        </div>
                    </div>
                </div>


                <div>
                    {{-- ─────────────────────────────────────
         JS : Toast + ควบคุม Bootstrap Modal
    ───────────────────────────────────── --}}
                    <script>
                    
                        document.addEventListener('DOMContentLoaded', () => {
                            const modal = new bootstrap.Modal(document.getElementById('product-modal'));

                            window.addEventListener('open-modal', () => modal.show());
                            window.addEventListener('close-modal', () => modal.hide());
                        });
                    </script>

                    {{-- ───────────── Toolbar (Search + Add) ───────────── --}}
                    <div class="row align-items-center g-2 mb-3">

                        {{-- Search --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>


                                <input type="text" class="form-control" placeholder="ค้นหาด้วยชื่อหรือรหัสสินค้า…"
                                   wire:model.live.debounce.500ms="search">
                            </div>
                        </div>

                        {{-- Add Button --}}
                        <div class="col-auto ms-auto">
                            <button class="btn btn-primary" wire:click="resetForm"
                                onclick="window.dispatchEvent(new Event('open-modal'))">
                                <i class="ri-add-line me-1"></i> เพิ่มสินค้า
                            </button>
                        </div>
                    </div>

                    {{-- ───────────── Card + Table ───────────── --}}
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="mb-0">รายการสินค้า</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-centered mb-0" id="inline-editable" style="font-size: 18px">
                                <thead class="">
                                    <tr>
                                        <th style="width:60px;">IDs</th>
                                        <th>รหัส</th>
                                        <th>ชื่อสินค้า</th>
                                        <th class="text-end">น้ำหนัก (kg)</th>
                                        <th class="text-end">ราคา (฿)</th>
                                        <th>ประเภท</th>
                                        <th>หน่วย</th>
                                        <th>สถานะ</th>
                                        <th style="width:130px;" class="text-center">จัดการ</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($products as $i => $p)
                                        <tr wire:key="row-{{ $p->product_id }}">
                                            <td>{{ $products->firstItem() + $i }}</td>
                                            <td>{{ $p->product_code }}</td>
                                            <td>{{ $p->product_name }}</td>
                                            <td class="text-end">{{ number_format($p->product_weight, 2) }}</td>
                                            <td class="text-end">{{ number_format($p->product_price, 2) }}</td>
                                            <td>{{ $p->product_type }}</td>
                                            <td>{{ $p->product_unit }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $p->product_status ? 'success' : 'secondary' }}">
                                                    {{ $p->product_status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning me-1"
                                                    wire:click="edit({{ $p->product_id }})">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirm('ยืนยันลบสินค้า?') || event.stopImmediatePropagation()"
                                                    wire:click="destroy({{ $p->product_id }})">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">— ไม่พบข้อมูล —</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{-- {{ $products->links() }} --}}
                            {{ $products->links( "pagination::bootstrap-5") }}
                            
                        </div>
                    </div>

                    {{-- ───────────── Bootstrap 5 Standard Modal ───────────── --}}
                    <div wire:ignore.self class="modal fade" id="product-modal" tabindex="-1"
                        aria-labelledby="productModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                {{-- Header --}}
                                <div class="modal-header">
                                    <h4 class="modal-title" id="productModalLabel">
                                        {{ $isEdit ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า' }}
                                    </h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                {{-- Form --}}
                                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            {{-- product_code --}}
                                            <div class="col-md-6">
                                                <label class="form-label">รหัสสินค้า *</label>
                                                <input type="text" wire:model.defer="product_code"
                                                    class="form-control @error('product_code') is-invalid @enderror">
                                                @error('product_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_name --}}
                                            <div class="col-md-6">
                                                <label class="form-label">ชื่อสินค้า *</label>
                                                <input type="text" wire:model.defer="product_name"
                                                    class="form-control @error('product_name') is-invalid @enderror">
                                                @error('product_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_weight --}}
                                            <div class="col-md-6">
                                                <label class="form-label">น้ำหนัก (kg)</label>
                                                <input type="number" step="0.01" wire:model.defer="product_weight"
                                                    class="form-control @error('product_weight') is-invalid @enderror">
                                                @error('product_weight')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_price --}}
                                            <div class="col-md-6">
                                                <label class="form-label">ราคา (฿) *</label>
                                                <input type="number" step="0.01" wire:model.defer="product_price"
                                                    class="form-control @error('product_price') is-invalid @enderror">
                                                @error('product_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_type --}}
                                            <div class="col-md-6">
                                                <label class="form-label">ประเภทสินค้า *</label>
                                                <input type="text" wire:model.defer="product_type"
                                                    class="form-control @error('product_type') is-invalid @enderror">
                                                @error('product_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_unit --}}
                                            <div class="col-md-6">
                                                <label class="form-label">หน่วย *</label>
                                                <input type="text" wire:model.defer="product_unit"
                                                    class="form-control @error('product_unit') is-invalid @enderror">
                                                @error('product_unit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_note --}}
                                            <div class="col-12">
                                                <label class="form-label">หมายเหตุ</label>
                                                <textarea rows="6" wire:model.defer="product_note" class="form-control"></textarea>
                                            </div>

                                            {{-- product_status --}}
                                            <div class="col-12 mt-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="statusSwitch" wire:model.defer="product_status">
                                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="submit" class="btn btn-success">
                                            {{ $isEdit ? 'อัปเดต' : 'บันทึก' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>






            </div>

{{-- ✅  คงไว้เฉพาะ script ใน @push --}}
@push('scripts')
<script>
  window.addEventListener('notify', e => {
      const {type='success', text=''} = e.detail;
      toastr.options = {timeOut: 3500, progressBar: true, positionClass:'toast-top-right'};
      toastr[type](text);
  });
</script>
@endpush
            