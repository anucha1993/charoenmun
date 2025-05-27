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
                                        <a href="javascript: void(0);"
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
                                @if ($selectedCustomer)
                                    <a href="#" wire:click.prevent="openDeliveryModal({{ $customer_id }})">+
                                        เพิ่มที่อยู่จัดส่ง</a>
                                @else
                                    <span class="text-danger">กรุณาเลือกลูกค้า</span>
                                @endif
                            </div>

                            {{-- <select wire:model.live="selected_delivery_id" class="form-select">
                                <option value="">-- ใช้ที่อยู่หลัก --</option>
                                @foreach ($customerDelivery as $item)
                                    <option value="{{ $item->id }}">{{ $item->delivery_number }}
                                        ({{ $item->delivery_contact_name }})
                                    </option>
                                @endforeach
                            </select> --}}

                            <select wire:model.live="selected_delivery_id" name="selected_delivery_id"
                                class="form-select">
                                <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                                @foreach ($customerDelivery as $delivery)
                                    <option value="{{ $delivery->id }}">
                                        {{ $delivery->delivery_contact_name }} - {{ $delivery->delivery_phone }}
                                    </option>
                                @endforeach
                            </select>



                            <address class="mt-2">
                                @if ($selectedDelivery)
                                    <b>ชื่อผู้ติดต่อ</b> {{ $selectedDelivery->delivery_contact_name }}
                                    ({{ $selectedDelivery->delivery_phone }}) </br>
                                    <b> ที่อยู่ : </b>{{ $selectedDelivery->delivery_number }}
                                    {{ $selectedDelivery->delivery_district_name }}
                                    {{ $selectedDelivery->delivery_amphur_name }}
                                    {{ $selectedDelivery->delivery_province_name }}
                                    {{ $selectedDelivery->delivery_zipcode }}

                                    <a href="javascript: void(0);"
                                        onclick="Livewire.dispatch('edit-delivery-modal', { deliveryId: {{ $delivery->id }} })">
                                        แก้ไข
                                    </a>
                                @else
                                    @if ($selectedCustomer)
                                        <b> ชื่อร้านค้า/ชื่อลูกค้า : </b>
                                        {{ $selectedCustomer->customer_contract_name }}
                                        ({{ $selectedCustomer->customer_phone }})<br>
                                        <b> ที่อยู่ :</b> {{ $selectedCustomer->customer_address }}
                                        {{ $selectedCustomer->customer_district_name }}
                                        {{ $selectedCustomer->customer_amphur_name }}
                                        {{ $selectedCustomer->customer_province_name }}
                                        {{ $selectedCustomer->customer_zipcode }}<br>
                                        <b> เลขประจำตัวผู้เสียภาษี :</b> {{ $selectedCustomer->customer_taxid }}
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
                                            <th>รายการสินค้า</th>
                                            <th>รายละเอียดสินค้า</th>
                                            <th>ความยาว</th>
                                            <th>น้ำหนัก</th>
                                            <th>จำนวน</th>
                                            <th>หน่วยนับ</th>
                                            <th>ราคา/หน่วย</th>
                                            <th class="text-end">รวมทั้งสิ้น</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($items as $i => $item)
                                            <tr class="align-top">
                                                <td class="align-top">{{ $i + 1 }}</td>
                                                <td style="min-width: 350px;">
                                                    <select class="form-select form-select-sm"
                                                        wire:model.live="items.{{ $i }}.product_id">
                                                        <option value="">-- เลือกสินค้า --</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->product_id }}">
                                                                {{ $product->product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>


                                                </td>
                                                <td style="min-width: 200px;"> {!! $item['product_detail'] ?? '' !!} </td>


                                                <td style="width: 110px">
                                                    <input type="text"
                                                        wire:model.live="items.{{ $i }}.product_length"
                                                        class="form-control form-control-sm">
                                                </td>
                                                <td style="width: 110px">
                                                    <input type="number" min="1"
                                                        wire:model.live="items.{{ $i }}.product_weight"
                                                        class="form-control form-control-sm" />
                                                </td>


                                                <td style="width: 110px">
                                                    <input type="number" min="1"
                                                        wire:model.live="items.{{ $i }}.quantity"
                                                        class="form-control form-control-sm" />
                                                </td>

                                                <td style="width: 100px">
                                                    <input type="text"
                                                        wire:model.live="items.{{ $i }}.product_unit"
                                                        class="form-control form-control-sm"
                                                        style="background-color: aliceblue" readonly>
                                                </td>
                                                <td style="width: 200px" class="text-end">
                                                    <input type="number" min="0" step="0.01"
                                                        wire:model.live="items.{{ $i }}.unit_price"
                                                        class="form-control form-control-sm text-end" />
                                                </td>

                                                <td class="text-end">
                                                    {{ number_format($item['total'], 2) }}
                                                </td>
                                                <td>
                                                    <a href="#" wire:click="removeItem({{ $i }})"><i
                                                            class="mdi mdi-trash-can text-danger"
                                                            style="font-size: 25px"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                       
                                    </tbody>
                                </table>
                                  <button type="button" class="btn btn-outline-success btn-sm mt-2"
                                                wire:click="addEmptyItem">
                                                ➕ เพิ่มรายการสินค้า
                                            </button>
                            </div> <!-- end table-responsive-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->



                    <hr>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" wire:model.live="enable_vat"
                            id="enableVatCheck">
                        <label class="form-check-label" for="enableVatCheck">
                            ✅ คำนวณ VAT 7%
                        </label>
                    </div>

                    @if ($enable_vat)
                        <div class="form-check mt-2 ms-3">
                            <input class="form-check-input" type="checkbox" wire:model.live="vat_included"
                                id="vatIncludedCheck">
                            <label class="form-check-label" for="vatIncludedCheck">
                                💡 คิดรวม VAT ในราคารวม (VAT-In)
                            </label>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted fs-14">Notes:</h6>
                                <small>
                                    <textarea wire:model="quote_note" class="form-control" cols="3" rows="3"></textarea>
                                </small>

                            </div>
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            <div class="row">

                                <div class="col-md-10">
                                    <p><b class="float-end">จำนวนเงินรวม:</b></p>
                                </div>
                                <div class="col-md-2">
                                   <span class="float-end">{{ number_format($subtotal, 2) }}</span>
                                </div>
                                 <div class="col-md-10">
                                    <p><b class="float-end">ภาษีมูลค่าเพิ่ม:</b></p>
                                </div>
                                <div class="col-md-2">
                                   <span class="float-end">{{ number_format($vat, 2) }}</span>
                                </div>
                                 <div class="col-md-10">
                                    <p><b class="float-end">จำนวนเงินทั้งสิ้น:</b></p>
                                </div>
                                <div class="col-md-2">
                                   <span class="float-end">{{ number_format($grand_total, 2) }}</span>
                                </div>

                            </div>
                         
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row-->

                    <div class="d-print-none mt-4">
                        <div class="text-center">
                            <a href="javascript:window.print()" class="btn btn-primary"><i
                                    class="ri-printer-line"></i>
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
    <livewire:quotations.delivery-address-modal />

    <div>
    </div>


    <script>
        document.addEventListener('open-delivery-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('bs-example-modal-lg'));
            modal.show();
        });
        document.addEventListener('close-delivery-modal', () => {
            const modalEl = document.getElementById('bs-example-modal-lg');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            // เคลียร์ backdrop และ class ที่ค้าง
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style = '';
            }, 300); // รอ animation จบก่อนค่อยเคลียร์
        });
    </script>

    <script>
        document.addEventListener('livewire:update', () => {
            $('#customerSelect').select2();
        });
    </script>


    <script>
        document.addEventListener('delivery-created-success', function(e) {
            const detail = e.detail?.[0] ?? {};
            const deliveryId = parseInt(detail.deliveryId);

            const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

            setTimeout(() => {
                const $dropdown = $("select[name='selected_delivery_id']");
                const found = $dropdown.find(`option[value='${deliveryId}']`).length > 0;

                console.log('🔍 Looking for delivery ID:', deliveryId, 'Found:', found);

                if (found) {
                    console.log('✅ Selecting delivery...');
                    $dropdown.val(deliveryId).trigger('change'); // or .trigger('change.select2') if Select2

                } else {
                    console.warn('❌ deliveryId not found in dropdown yet');
                }
            }, 500);
        });
    </script>>

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
