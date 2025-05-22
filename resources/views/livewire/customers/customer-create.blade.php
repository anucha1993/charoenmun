
<div>

    <br>

    {{-- <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
     --}}

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ข้อมูลลูกค้า</h4>
                    <p class="text-muted mb-0">
                        รายละเอียดข้อมูลลูกค้า <code>Customer</code>.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="" wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6  mb-2">
                                    <label for="">รหัสลูกค้า </label>
                                    <input type="text" wire:model="customer_code" class="form-control"
                                        style="background-color: aliceblue" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="">ประเภทลูกค้า <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="customer_type" required>
                                        <option value="">--กรุณาเลือก--</option>
                                        @foreach ($customerType as $option)
                                            <option value="{{ $option->id }}">{{ $option->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">ระดับลุกค้า <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="customer_level" required>
                                        <option value="">--กรุณาเลือก--</option>
                                        @foreach ($customerLevel as $option)
                                            <option value="{{ $option->id }}">{{ $option->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">ชื่อลูกค้า <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="customer_name" id="" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">เลขประจำตัวผู้เสียภาษี</label>
                                    <input type="text" wire:model="customer_taxid" id=""
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="">ชื่อผู้ติดต่อ</label>
                                    <input type="text" wire:model="customer_contract_name" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">เบอร์โทร</label>
                                    <input type="text" wire:model="customer_phone" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Email</label>
                                    <input type="text" wire:model="customer_email" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Line ID</label>
                                    <input type="text" wire:model="customer_idline" id=""
                                        class="form-control">
                                </div>

                            </div>
                            <div class="row">
                                <h5>ที่อยู่ลูกค้า</h5>
                                <hr>

                                <div class="col-md-12 mb-2">
                                    <label for="">เลขที่/หมู่/ซอย </label>
                                    <input type="text" wire:model="customer_address" id=""
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="">จังหวัด</label>
                                    <select class="form-select" wire:model.live="customer_province" required>
                                        <option value="">-- เลือกจังหวัด --</option>
                                        @foreach ($provinces as $code => $name)
                                            <option value="{{ $code }}">{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="">อำเภอ/เขต</label>
                                    <select class="form-select" wire:model.live="customer_amphur"
                                        @disabled(!$amphures) required>
                                        <option value="">-- เลือกอำเภอ --</option>
                                        @foreach ($amphures as $code => $name)
                                            <option value="{{ $code }}">{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="">ตำบล/แขวง</label>
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
                                    <label for="">รหัสไปรษณีย์ <span class="text-primary"
                                            style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                                    <input type="text" wire:model.live.debounce.500ms="customer_zipcode"
                                        id="" class="form-control">
                                </div>


                            </div>

                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ที่อยู่จัดส่งสินค้า</h4>
                    <p class="text-muted mb-0">
                        ที่อยู่จัดส่งสินค้า <code>Customer</code>.
                    </p>

                </div>

                <div class="card-body">
                 
                    <div class="row">

                      

                    </div>
                </div>
            </div>
        </div> --}}



    </div>






    

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                message
            }) => {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right", // หรือ 'toast-bottom-right'
                    "timeOut": "3000"
                };
                toastr.success(message);
            });
        });
    </script>
