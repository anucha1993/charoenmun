<div class="container-fluid">
    {{-- ───────── Toast + Modal Controller (ย้ายมา stack ด้านล่าง) ───────── --}}

    {{-- main card --------------------------------------------------------- --}}
    <br>
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">เพิ่มข้อมูลลูกค้า / ร้านค้า</h4>
                <p class="text-muted mb-0">กรุณากรอกข้อมูลให้ครบถ้วน <code>เพื่อจัดเก็บข้อมูลลูกค้า</code>
                    ให้ถูกต้องสมบูรณ์ที่สุด.</p>

                <button class="btn btn-success float-end" type="submit" wire:loading.attr="disabled" form="saveCustomer">
                    <span wire:loading.remove wire:target="saveCustomer">💾 Save</span>
                    <span wire:loading wire:target="saveCustomer" class="spinner-border spinner-border-sm"></span>
                </button>

            </div>


            <div class="card-body">
                <form wire:submit.prevent="saveCustomer" id="saveCustomer">
                    {{-- row 1 -------------------------------------------------- --}}
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ประเภทลูกค้า <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="customer_type" required>
                                <option value="">--กรุณาเลือก--</option>
                                @foreach ($customerType as $option)
                                    <option value="{{ $option }}">{{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ระดับ <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="customer_level" required>
                                <option value="">--กรุณาเลือก--</option>
                                @foreach ($customerLevel as $option)
                                    <option value="{{ $option }}">{{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- row 2 -------------------------------------------------- --}}
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">เลขประจำตัวผู้เสียภาษี <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_taxid"
                                placeholder="เลขประจำตัวผู้เสียภาษี">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ชื่อลูกค้า/ร้านค้า <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_name"
                                placeholder="ชื่อลูกค้า/ร้านค้า" required>
                        </div>
                    </div>

                    {{-- row 3 -------------------------------------------------- --}}
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ชื่อผู้ติดต่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_contract_name"
                                placeholder="ชื่อผู้ติดต่อ" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">เบอร์ติดต่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_phone" placeholder="+66"
                                required>
                        </div>
                    </div>

                    {{-- row 4 -------------------------------------------------- --}}
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-control" wire:model="customer_email"
                                placeholder="email@example.com">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Line id <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_idline" placeholder="@"
                                required>
                        </div>
                    </div>

                    <hr>

                    {{-- ที่อยู่ใบเสร็จ -------------------------------------------------- --}}
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ที่อยู่ / เลขที่ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="customer_address"
                                placeholder="เลขที่ / ซอย / ถนน">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.live="customer_province" required>
                                <option value="">-- เลือกจังหวัด --</option>
                                @foreach ($provinces as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">อำเภอ <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.live="customer_amphur" @disabled(!$amphures)
                                required>
                                <option value="">-- เลือกอำเภอ --</option>
                                @foreach ($amphures as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">ตำบล <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.live="customer_district"
                                @disabled(!$districts) required>
                                <option value="">-- เลือกตำบล --</option>
                                @foreach ($districts as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">รหัสไปรษณีย์ <span class="text-primary"
                                    style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                            <input type="text" class="form-control"
                                wire:model.live.debounce.500ms="customer_zipcode" maxlength="5"
                                placeholder="รหัสไปรษณีย์" required>
                        </div>
                    </div>

                    {{-- SAVE BUTTON -------------------------------------------------- --}}

                </form>
            </div>

        </div>
    </div>
