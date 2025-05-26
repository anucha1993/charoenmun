<div>
    <br>
    <br>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Invoice Logo-->
                    <div class="clearfix">
                        <div class="float-start mb-0">
                            <img src="/images/logo-crrtm.png" alt="dark logo" height="100">
                        </div>
                        <div class="float-end">
                            <h4 class="m-0 d-print-none">Quotation / ใบเสนอราคา</h4>
                        </div>
                    </div>

                    <!-- Invoice Detail-->
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="float mt-3 mb-3">
                                <p><b>บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)</b></p>
                                ที่อยู่ 99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง จังหวัดนนทบุรี 11110
                                </br>โทร 082-4789197 เลขประจำตัวผู้เสียภาษี 0125560015546

                            </div>

                        </div><!-- end col -->
                        <div class="col-sm-5 offset-sm-2">
                            <div class="mt-3 float-sm-end">

                                <div class="mb-1">
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text" id="basic-addon1">วันที่เสนอราคา :</span>
                                        <input type="date" class="form-control col-form-label-lg"
                                            placeholder="Username" aria-label="Username"
                                            aria-describedby="basic-addon1">
                                    </div>
                                </div>



                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->
                    {{-- @dump($customer_id)
                    @dump($selectedCustomer) --}}
                    <div class="row mt-1">
                        <div class="col-6">
                            <h6 class="fs-14">ข้อมูลลูกค้า / Billing Address</h6>
                            <div>
                                <a href="#" onclick="Livewire.dispatch('create-customer')">+ เพิ่มลูกค้า</a>

                            </div>


                            <div>
                                <select wire:model="customer_id" id="customerSelect" class="form-control">
                                    <option value="">-- เลือกลูกค้า --</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}">{{ $item->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <address class="mt-2">
                                @if ($selectedCustomer)
                                    <b> ชื่อร้านค้า/ชื่อลูกค้า :</b> {{ $selectedCustomer->customer_contract_name }}
                                    ({{ $selectedCustomer->customer_phone }})<br>
                                    <b> ที่อยู่ :</b> {{ $selectedCustomer->customer_address }}
                                    {{ $selectedCustomer->customer_district_name }}
                                    {{ $selectedCustomer->customer_amphur_name }}
                                    {{ $selectedCustomer->customer_province_name }}
                                    {{ $selectedCustomer->customer_zipcode }}<br>
                                    <b> เลขประจำตัวผู้เสียภาษี :</b> {{ $selectedCustomer->customer_taxid }}
                                    @if ($customer_id)
                                        <a href="#"
                                            onclick="Livewire.dispatch('edit-customer', { id: {{ $customer_id }} })">
                                            แก้ไข
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted">กรุณาเลือกลูกค้า</span>
                                @endif
                            </address>

                        </div> <!-- end col-->


                        <div class="col-6">
                            <h6 class="fs-14">ข้อมูลจัดส่ง / Shipping Address</h6>
                            <div>
                                <a href="#" wire:click="$emit('openDeliveryModal', {{ $customer_id }})">+
                                    เพิ่มที่อยู่จัดส่ง</a>
                            </div>

                            <select wire:model.live="selected_delivery_id" class="form-select">
                                <option value="">-- ใช้ที่อยู่หลัก --</option>
                                @foreach ($customerDelivery as $item)
                                    <option value="{{ $item->id }}">{{ $item->delivery_number }}
                                        ({{ $item->delivery_contact_name }})
                                    </option>
                                @endforeach
                            </select>



                            <address class="">
                                @if ($selectedDelivery)
                                    <b>ชื่อผู้ติดต่อ</b> {{ $selectedDelivery->delivery_contact_name }}
                                    ({{ $selectedDelivery->delivery_phone }}) </br>
                                    <b> ที่อยู่ : </b>{{ $selectedDelivery->delivery_number }}
                                    {{ $selectedDelivery->delivery_district_name }}
                                    {{ $selectedDelivery->delivery_amphur_name }}
                                    {{ $selectedDelivery->delivery_province_name }}
                                    {{ $selectedDelivery->delivery_zipcode }}<br>
                                @else
                                    @if ($selectedCustomer)
                                        <b> ชื่อร้านค้า/ชื่อลูกค้า :</b>
                                        {{ $selectedCustomer->customer_contract_name }}
                                        ({{ $selectedCustomer->customer_phone }})<br>
                                        <b> ที่อยู่ :</b> {{ $selectedCustomer->customer_address }}
                                        {{ $selectedCustomer->customer_district_name }}
                                        {{ $selectedCustomer->customer_amphur_name }}
                                        {{ $selectedCustomer->customer_province_name }}
                                        {{ $selectedCustomer->customer_zipcode }}<br>
                                        <b> เลขประจำตัวผู้เสียภาษี :</b> {{ $selectedCustomer->customer_taxid }}
                                        {{-- <a href="#"
                                            wire:click="$emit('editDeliveryModal', {{ $selected_delivery_id }})">แก้ไข</a> --}}
                                    @endif
                                @endif
                            </address>
                        </div> <!-- end col-->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                                    <thead class="border-top border-bottom bg-light-subtle border-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Unit Cost</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="">1</td>
                                            <td>
                                                <b>Laptop</b> <br />
                                                Brand Model VGN-TXN27N/B
                                                11.1" Notebook PC
                                            </td>
                                            <td>1</td>
                                            <td>$1799.00</td>
                                            <td class="text-end">$1799.00</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>
                                                <b>Warranty</b> <br />
                                                Two Year Extended Warranty -
                                                Parts and Labor
                                            </td>
                                            <td class="">3</td>
                                            <td>$499.00</td>
                                            <td class="text-end">$1497.00</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>
                                                <b>LED</b> <br />
                                                80cm (32) HD Ready LED TV
                                            </td>
                                            <td class="">2</td>
                                            <td>$412.00</td>
                                            <td class="text-end">$824.00</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted fs-14">Notes:</h6>
                                <small>
                                    All accounts are to be paid within 7 days from receipt of
                                    invoice. To be paid by cheque or credit card or direct payment
                                    online. If account is not paid within 7 days the credits details
                                    supplied as confirmation of work undertaken will be charged the
                                    agreed quoted fee noted above.
                                </small>
                            </div>
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            <div class="float-end mt-3 mt-sm-0">
                                <p><b>Sub-total:</b> <span class="float-end">$4120.00</span></p>
                                <p><b>VAT (12.5):</b> <span class="float-end">$515.00</span></p>
                                <h3>$4635.00 USD</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row-->

                    <div class="d-print-none mt-4">
                        <div class="text-center">
                            <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line"></i>
                                Print</a>
                            <a href="javascript: void(0);" class="btn btn-info">Submit</a>
                        </div>
                    </div>
                    <!-- end buttons -->

                </div> <!-- end card-body-->
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>

    <livewire:quotations.customer-modal />

    <div>
    </div>

    <script>
        document.addEventListener('livewire:update', () => {
            $('#customerSelect').select2();
        });
    </script>

    <script>
        document.addEventListener('customer-created-success', function(e) {
            const detail = e.detail?.[0] ?? {};
            const customerId = parseInt(detail.customerId);
            console.log('✅ Parsed ID:', customerId);

            const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

            // ✅ เรียก refreshCustomers → รอ render เสร็จ → ค่อย select
            livewireComponent.call('refreshCustomers').then(() => {
                // ✅ รอ 300ms เพื่อให้ Blade render dropdown ใหม่เสร็จ
                setTimeout(() => {
                    // ✅ ตรวจว่าลูกค้าใหม่ปรากฏใน dropdown แล้วหรือยัง
                    const found = $(`#customerSelect option[value="${customerId}"]`).length > 0;

                    if (found) {
                        console.log('✅ New customer found in <select>. Now selecting...');
                        $('#customerSelect').val(customerId).trigger('change');
                        livewireComponent.call('setCustomerId', customerId);
                    } else {
                        console.warn('❌ New customer not found in <select> yet.');
                    }
                }, 300); // เพิ่ม delay ให้แน่ใจว่า Blade render เสร็จ
            });
        });
    </script>


    <script>
        document.addEventListener('open-customer-modal', () => {
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        });
        document.addEventListener('close-customer-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
            if (modal) {
                modal.hide();
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let select = $('#customerSelect');
            select.select2();

            select.on('change', function() {
                let selectedId = $(this).val();
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute(
                    'wire:id'));
                livewireComponent.call('setCustomerId', selectedId);
            });
        });
    </script>
