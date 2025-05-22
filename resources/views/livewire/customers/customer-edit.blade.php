<div>

    <br>

    <div class="row">
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-pink">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-eye-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">ยอดคำสั่งซื้อ</h6>
                    <h2 class="my-2">0.00</h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">ราคาบาท</span>
                        <span class="text-nowrap">แสดงราคาทั้งหมดของคำสั่งซื้อ</span>
                    </p>
                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-purple">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-wallet-2-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">จำนวนใบสั่งซื้อ</h6>
                    <h2 class="my-2">0.00</h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">ราคาบาท</span>
                        <span class="text-nowrap">แสดงจำนวนใบสั่งซื้อทั้งหมด</span>
                    </p>
                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-info">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-shopping-basket-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">สินค้ารอส่ง</h6>
                    <h2 class="my-2">0.00</h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-25 me-1">จำนวนใบสั่งซื้อรอจัดส่ง</span>
                        <span class="text-nowrap">คิดจากคำสั่งซื้อทั้งหมด</span>
                    </p>
                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-primary">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-group-2-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">Pocket Money</h6>
                    <h2 class="my-2">
                        <strong>{{ number_format($customer_pocket_money, 2) }} บาท</strong>
                    </h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">จำนเงินคงเหลือ</span>
                        <a href="#" class="text-warning" wire:click="$dispatch('openPocketMoneyModal')"
                            class="">
                            แก้ไขวงเงิน
                        </a>
                    </p>

                </div>
            </div>
        </div> <!-- end col-->
    </div>




    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ข้อมูลลูกค้า</h4>
                    <p class="text-muted mb-0">
                        รายละเอียดข้อมูลลูกค้า <code>Customer</code>.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="" wire:submit.prevent="update">
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

 

 



        <div class="col-xl-6">


            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ประวัติการจ่ายเงิน Pocket Money</h4>
                    <p class="text-muted mb-0">
                        ประวัติการจ่ายเงิน <code> Pocket Money </code>.
                    </p>
 
                </div>
                <div class="card-body">
                    <div class="responsive">
                    <table class="table table">
                        <thead>
                            <tr>
                            <th>วันที่ส่งตรวจ</th>
                            <th>ผู้โอน</th>
                            <th>ผู้รับ</th>
                            <th>จำนวนเงิน</th>
                            <th>ประเภทสลิป</th>
                            <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>19 พ.ค. 256807:57น.</td>
                                <td>นาง ปิยมาส มะโนรมณ์ xxx-x-xx141-5<br></td>
                                <td>MR. ANUCHA YOTHANAN 307-3-13923-4<br></td>
                                <td>42,450</td>
                                <td class="text-success">สลิปถูกต้อง</td>
                                <td class="text-success">ยืนยันยอดแล้ว</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
      


            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ที่อยู่จัดส่งสินค้า</h4>
                    <p class="text-muted mb-0">
                        ที่อยู่จัดส่งสินค้า <code>Customer</code>.
                    </p>

                </div>

                <div class="card-body">
                    <button type="button" wire:click="openDeliveryModal" class="btn btn-success btn-sm mb-2">
                        + เพิ่มที่อยู่
                    </button>
                    <div class="row">

                        @foreach ($deliveryAddresses as $index => $address)
                            <ul class="list-group" style="font-size: 16px">
                                <li class="list-group-item">
                                    <b>ชื่อลูกค้า : {{ $address['delivery_contact_name'] ?? '-' }}
                                        ({{ $address['delivery_phone'] ?? '-' }})
                                    </b> </br>
                                    เลขที่ {{ $address['delivery_number'] ?? '-' }}
                                    {{ $address['delivery_district_name'] ?? '-' }}
                                    {{ $address['delivery_amphur_name'] ?? '-' }}
                                    {{ $address['delivery_province_name'] ?? '-' }}
                                    {{ $address['delivery_zipcode'] ?? '-' }}
                                    </br>
                                    <button type="button" class="btn btn-sm btn-info"
                                        wire:click="openDeliveryModal({{ $index }})">แก้ไข</button>
                                    <button type="button"
                                        onclick="if (confirm('คุณต้องการลบรายการนี้หรือไม่?') === false) event.stopImmediatePropagation();"
                                        wire:click="removeDelivery({{ $index }})"
                                        class="btn btn-sm btn-danger">
                                        ลบ
                                    </button>
                                </li>

                            </ul>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>



    </div>

    <!-- Modal: แก้ไข Pocket Money -->
    <div wire:ignore.self class="modal fade" id="pocketMoneyModal" tabindex="-1" role="dialog"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form wire:submit.prevent="savePocketMoney">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขวงเงิน (Pocket Money)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pocket_money" class="form-label">วงเงินใหม่</label>
                            <input type="number" class="form-control" wire:model="customer_pocket_money">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">รหัสยืนยันการแก้ไข</label>
                            <input type="password" class="form-control" wire:model="confirm_password" placeholder="***********">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </div>
            </form>
        </div>
    </div>






    <div id="signup-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>เพิ่ม/แก้ไข ข้อมูลจัดส่งสินค้า</h5>
                </div>
                <div class="modal-body">


                    <form wire:submit.prevent="saveDelivery">

                        <div class="mb-2">
                            <label for="username" class="form-label">ชื่อผู้ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_contact_name"
                                type="text" placeholder="ชื่อผู้ติดต่อ">
                        </div>
                        <div class="mb-2">
                            <label for="username" class="form-label">เบอร์ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_phone" type="text"
                                placeholder="เบอร์ติดต่อ">
                        </div>

                        <div class="mb-2">
                            <label for="username" class="form-label">เลขที่/หมู่/ซอย</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_number" type="text"
                                placeholder="เลขที่/หมู่/ซอย">
                        </div>

                        <!-- จังหวัด -->
                        <div class="mb-2">
                            <label for="">จังหวัด</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_province" required>
                                <option value="">-- เลือกจังหวัด --</option>
                                @foreach ($deliveryProvinces as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- อำเภอ -->
                        <div class="mb-2">
                            <label for="">อำเภอ/เขต</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_amphur"
                                @disabled(!$deliveryAmphures) required>
                                <option value="">-- เลือกอำเภอ --</option>
                                @foreach ($deliveryAmphures as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ตำบล -->
                        <div class="mb-2">
                            <label for="">ตำบล/แขวง</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_district"
                                @disabled(!$deliveryDistricts) required>
                                <option value="">-- เลือกตำบล --</option>
                                @foreach ($deliveryDistricts as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- รหัสไปรษณีย์ -->
                        <div class="mb-2">
                            <label for="">รหัสไปรษณีย์ <span class="text-primary"
                                    style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                            <input type="text" wire:model.live.debounce.500ms="deliveryForm.delivery_zipcode"
                                class="form-control">
                        </div>



                        <div class="mb-3 text-center">
                            <button ะ class="btn btn-primary" type="submit">บันทึก</button>
                        </div>

                    </form>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script>
        function confirmDelete(id) {
            if (confirm('ยืนยันลบที่อยู่นี้ใช่หรือไม่?')) {
                Livewire.dispatch('removeDelivery', {
                    id: id
                });
            }
        }
    </script>


    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openPocketMoneyModal', () => {
                new bootstrap.Modal(document.getElementById('pocketMoneyModal')).show();
            });
            Livewire.on('closePocketMoneyModal', () => {
                bootstrap.Modal.getInstance(document.getElementById('pocketMoneyModal'))?.hide();
            });
        });
    </script>


    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openModal', () => {
                const modalEl = document.getElementById('signup-modal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });

            Livewire.on('closeModal', () => {
                const modalEl = document.getElementById('signup-modal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                message,
                type = 'success'
            }) => {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "3000"
                };

                if (type === 'error') {
                    toastr.error(message);
                } else if (type === 'warning') {
                    toastr.warning(message);
                } else if (type === 'info') {
                    toastr.info(message);
                } else {
                    toastr.success(message);
                }
            });
        });
    </script>
