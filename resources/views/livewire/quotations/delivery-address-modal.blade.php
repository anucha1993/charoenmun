<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                          @if ($editing)
                                แก้ไขที่อยู่จัดส่ง
                            @else
                               เพิ่มที่อยู่จัดส่ง
                            @endif
                        
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal" onclick="cleanupModal('deliveryModal')"></button>
                </div>
                <div class="modal-body">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- 🧾 แสดงข้อมูลที่โหลดจาก customer --}}
                    <div class="mb-3">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <input type="text" class="form-control" wire:model="customer_name" readonly style="background-color: #f8f9fa;">
                    </div>


                    <form id="delivery-form" wire:submit.prevent="{{ $editing ? 'updateDelivery' : 'saveDelivery' }}">

                        <div class="mb-2">
                            <label for="delivery_contact_name" class="form-label">ชื่อผู้ติดต่อ <span class="text-danger">*</span></label>
                            <input class="form-control @error('deliveryForm.delivery_contact_name') is-invalid @enderror" 
                                wire:model="deliveryForm.delivery_contact_name" type="text"
                                placeholder="ชื่อผู้ติดต่อ" required>
                            @error('deliveryForm.delivery_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="delivery_phone" class="form-label">เบอร์ติดต่อ</label>
                            <input class="form-control @error('deliveryForm.delivery_phone') is-invalid @enderror" 
                                wire:model="deliveryForm.delivery_phone" type="text"
                                placeholder="เบอร์ติดต่อ">
                            @error('deliveryForm.delivery_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ที่อยู่จัดส่ง -->
                        <div class="mb-2">
                            <label for="delivery_address" class="form-label">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control @error('deliveryForm.delivery_address') is-invalid @enderror" 
                                wire:model="deliveryForm.delivery_address" 
                                rows="4" placeholder="กรอกที่อยู่จัดส่งแบบเต็ม (เลขที่ หมู่ ซอย ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์)"></textarea>
                            @error('deliveryForm.delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal"
                        data-bs-dismiss="modal">ปิด</button>
                    @if ($editing)
                        <button type="submit" class="btn btn-warning" form="delivery-form">อัปเดต</button>
                    @else
                        <button type="submit" class="btn btn-primary" form="delivery-form">บันทึก</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
