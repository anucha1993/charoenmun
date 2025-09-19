<div>


    
    <br>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">{{ $editMode ? 'แก้ไขผู้ใช้งาน' : 'เพิ่มผู้ใช้งาน' }}</h4>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> กลับ
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.live="state.name" required>
                            @error('state.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" wire:model.live="state.email" required>
                            @error('state.email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ประเภทผู้ใช้ <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.live="state.type" required>
                                <option value="">เลือกประเภท</option>
                                <option value="admin">ผู้ดูแลระบบ</option>
                                <option value="member">สมาชิก</option>
                            </select>
                            @error('state.type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        
                        @if($editMode)
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model.live="resetPassword" id="resetPassword">
                                <label class="form-check-label" for="resetPassword">
                                    เปลี่ยนรหัสผ่าน
                                </label>
                            </div>
                        </div>

                        @if($resetPassword)
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model.live="state.password" required>
                            @error('state.password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model.live="state.password_confirmation" required>
                            @error('state.password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @endif
                        @endif

                        @if(!$editMode)
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model.live="state.password" required>
                            @error('state.password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" wire:model.live="state.password_confirmation" required>
                            @error('state.password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <div class="text-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
