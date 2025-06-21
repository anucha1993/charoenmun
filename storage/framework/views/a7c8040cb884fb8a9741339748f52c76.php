<div>
    <br>
    <br>
    <!-- end page title -->

    <?php use App\Enums\QuotationStatus; ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form wire:submit.prevent="save">
                    <div class="card-body">

                        <!-- Invoice Logo-->
                        <div class="clearfix">
                            <div class="float-start mb-0">
                                <img src="/images/logo-crrtm.png" alt="dark logo" height="100">
                            </div>


                            <div class="float-end">
                                <div class="text-center">
                                    <h4 class="m-0 d-print-none">Quotation / ใบเสนอราคา</h4>
                                    <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                                        <img src="<?php echo e(route('qr.quotation', $quotation->id)); ?>" alt="QR"
                                            style="height:100px;">

                                        <h4 class="m-0 d-print-none"><?php echo e($quotation->quote_number); ?></h4>
                                        <?php echo quote_status_badge($quotation->quote_status); ?>

                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


                                </div>

                            </div>
                        </div>

                        <!-- Invoice Detail-->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="float mt-3 mb-3">
                                    <p>
                                        <b>บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)</b>
                                    </p>
                                    ที่อยู่ 99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง จังหวัดนนทบุรี 11110
                                    </br> โทร 082-4789197 เลขประจำตัวผู้เสียภาษี 0125560015546
                                </div>



                            </div><!-- end col -->
                            
                            <div class="col-sm-5 offset-sm-2">
                                <div class="mt-3 float-sm-end">


                                    <!--[if BLOCK]><![endif]--><?php if($quotation && $quote_status === 'wait'): ?>
                                        <button type="button" class="btn btn-sm btn-info mb-1 float-end"
                                            wire:click="approveQuotation(<?php echo e($quotation->id); ?>)"
                                            onclick="return confirm('ยืนยันการอนุมัติใบเสนอราคา เลขที่ <?php echo e($quotation->quote_number); ?> ?') || event.stopImmediatePropagation()">
                                            อนุมัติใบเสนอราคา
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div class="mb-1">
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text" id="basic-addon1">วันที่ออกเอกสาร :</span>
                                            <input type="date" class="form-control col-form-label-lg"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                wire:model="quote_date" aria-describedby="basic-addon1">
                                        </div>
                                    </div>

                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
                        
                        <div class="row mt-1">
                            <div class="col-6">
                                <h6 class="fs-14">ข้อมูลลูกค้า / Billing Address</h6>
                                <div>
                                    <a href="#" onclick="Livewire.dispatch('create-customer')">+ เพิ่มลูกค้า</a>

                                </div>
                                <div>

                                    <select id="customerSelect" class="form-control"
                                        <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>>
                                        <option value="">-- เลือกลูกค้า --</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($c->id); ?>" <?php if($c->id == $customer_id): echo 'selected'; endif; ?>>
                                                <?php echo e($c->customer_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>

                                </div>

                                <address class="mt-2">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                        <b> ชื่อร้านค้า/ชื่อลูกค้า :</b> <?php echo e($selectedCustomer->customer_contract_name); ?>

                                        (<?php echo e($selectedCustomer->customer_phone); ?>)<br>
                                        <b> ที่อยู่ :</b> <?php echo e($selectedCustomer->customer_address); ?>

                                        <?php echo e($selectedCustomer->customer_district_name); ?>

                                        <?php echo e($selectedCustomer->customer_amphur_name); ?>

                                        <?php echo e($selectedCustomer->customer_province_name); ?>

                                        <?php echo e($selectedCustomer->customer_zipcode); ?><br>
                                        <b> เลขประจำตัวผู้เสียภาษี :</b> <?php echo e($selectedCustomer->customer_taxid); ?>

                                        <!--[if BLOCK]><![endif]--><?php if($customer_id): ?>
                                            <a href="javascript: void(0);"
                                                onclick="Livewire.dispatch('edit-customer', { id: <?php echo e($customer_id); ?> })">
                                                แก้ไข
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php else: ?>
                                        <span class="text-muted">กรุณาเลือกลูกค้า</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </address>

                            </div> <!-- end col-->


                            <div class="col-6">
                                <h6 class="fs-14">ข้อมูลจัดส่ง / Shipping Address</h6>
                                <div>
                                    <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                        <a href="#" wire:click.prevent="openDeliveryModal(<?php echo e($customer_id); ?>)">+
                                            เพิ่มที่อยู่จัดส่ง</a>
                                    <?php else: ?>
                                        <span class="text-danger">กรุณาเลือกลูกค้า</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>



                                <select wire:model.live="selected_delivery_id" name="selected_delivery_id"
                                    <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?> class="form-select">
                                    <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <option value="<?php echo e($delivery->id); ?>" <?php if($delivery->id == $selected_delivery_id): ?> selected <?php endif; ?>>
                                            <?php echo e($delivery->delivery_contact_name); ?> - <?php echo e($delivery->delivery_phone); ?>

                                        </option>
                                       
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>



                                <address class="mt-2">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedDelivery): ?>
                                        <b>ชื่อผู้ติดต่อ</b> <?php echo e($selectedDelivery->delivery_contact_name); ?>

                                        (<?php echo e($selectedDelivery->delivery_phone); ?>) </br>
                                        <b> ที่อยู่ : </b><?php echo e($selectedDelivery->delivery_number); ?>

                                        <?php echo e($selectedDelivery->delivery_district_name); ?>

                                        <?php echo e($selectedDelivery->delivery_amphur_name); ?>

                                        <?php echo e($selectedDelivery->delivery_province_name); ?>

                                        <?php echo e($selectedDelivery->delivery_zipcode); ?>


                                        <a href="javascript: void(0);"
                                            onclick="Livewire.dispatch('edit-delivery-modal', { deliveryId: <?php echo e($delivery->id); ?> })">
                                            แก้ไข
                                        </a>
                                    <?php else: ?>
                                        <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                            <b> ชื่อร้านค้า/ชื่อลูกค้า : </b>
                                            <?php echo e($selectedCustomer->customer_contract_name); ?>

                                            (<?php echo e($selectedCustomer->customer_phone); ?>)<br>
                                            <b> ที่อยู่ :</b> <?php echo e($selectedCustomer->customer_address); ?>

                                            <?php echo e($selectedCustomer->customer_district_name); ?>

                                            <?php echo e($selectedCustomer->customer_amphur_name); ?>

                                            <?php echo e($selectedCustomer->customer_province_name); ?>

                                            <?php echo e($selectedCustomer->customer_zipcode); ?><br>
                                            <b> เลขประจำตัวผู้เสียภาษี :</b> <?php echo e($selectedCustomer->customer_taxid); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </address>
                            </div> <!-- end col-->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <div class="table">
                                    <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                                        <thead class="border-top border-bottom bg-light-subtle border-light">
                                            <tr>
                                                <th>#</th>
                                                <th>รายการสินค้า</th>
                                                <th>รายละเอียดสินค้า</th>
                                                <th>Vat</th>
                                                <th>ความยาว</th>
                                                
                                                
                                                <th>จำนวน</th>
                                                <th>หน่วยนับ</th>
                                                <th>ราคา/หน่วย</th>
                                                <th class="text-end">รวมทั้งสิ้น</th>
                                            </tr>
                                        </thead>
                                        <tbody>



                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="align-top" wire:key="row-<?php echo e($i); ?>">
                                                    <td class="align-top"><?php echo e($i + 1); ?></td>
                                                    <td style="min-width: 350px;">

                                                        <div class="position-relative" wire:ignore.self>
                                                            <input type="text"
                                                                class="form-control form-control-sm mb-1 text-black"
                                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                                placeholder="ค้นหาสินค้า..."
                                                                wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.product_search"
                                                                wire:keydown.escape="$set('items.<?php echo e($i); ?>.product_results', [])"
                                                                wire:focus="$set('items.<?php echo e($i); ?>.product_results_visible', true)"
                                                                wire:key="search-<?php echo e($i); ?>"
                                                                 />

                                                            <input type="text"
                                                                wire:model="items.<?php echo e($i); ?>.product_note"
                                                                class="form-control form-control-sm"
                                                                placeholder="หมายเหตุ">

                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($item['product_results_visible'])): ?>
                                                                <ul class="list-group position-absolute shadow"
                                                                    style="max-height: 400px; overflow-y: auto; z-index: 999999;">
                                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['product_results']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <a href="javascript: void(0);">
                                                                            <li class="list-group-item list-group-item-action"
                                                                                wire:click="selectProduct(<?php echo e($i); ?>, <?php echo e($result->product_id); ?>, <?php echo \Illuminate\Support\Js::from($result->product_name)->toHtml() ?>)">
                                                                                <?php echo e($result->product_name); ?>

                                                                                (<?php echo e($result->product_size); ?>)
                                                                                <?php echo e($result->productWireType?->value ?? '-'); ?>

                                                                            </li>
                                                                        </a>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                </ul>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>


                                                        



                                                    </td>


                                                    <td style="min-width:200px;">
                                                        <!--[if BLOCK]><![endif]--><?php if($item['product_calculation'] != 1): ?>
                                                            <span>ความหนา:</span>
                                                            <input type="number" step="0.01"
                                                                wire:model.debounce.300ms="items.<?php echo e($i); ?>.product_calculation"
                                                                class="form-control form-control-sm"
                                                                style="display:inline-block; width:80px; vertical-align:middle;"
                                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?> />
                                                        <?php else: ?>
                                                            <?php echo $item['product_detail'] ?? ''; ?>

                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td>
                                                        <input type="checkbox"
                                                            wire:model.live="items.<?php echo e($i); ?>.product_vat"
                                                            wire:change="refreshVat">

                                                    </td>


                                                    <td style="width: 110px">

                                                        <input type="text"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                            wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.product_length"
                                                            class="form-control form-control-sm">
                                                    </td>
                                                    

                                                    <td style="display: none">

                                                        <input type="number"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                            wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.product_weight"
                                                            class="form-control form-control-sm" />
                                                    </td>


                                                    <td style="width: 110px">
                                                        <input type="number" min="1"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                            wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.quantity"
                                                            class="form-control form-control-sm" />
                                                    </td>

                                                    <td style="width: 100px">
                                                        <input type="text"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                            wire:model.live="items.<?php echo e($i); ?>.product_unit"
                                                            class="form-control form-control-sm"
                                                            style="background-color: aliceblue" readonly>
                                                    </td>
                                                    <td style="width: 200px" class="text-end">

                                                        <input type="number" min="0" step="0.01"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                            wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.unit_price"
                                                            class="form-control form-control-sm text-end" />

                                                    </td>

                                                    <td class="text-end">
                                                        <?php echo e(number_format($item['total'], 2)); ?>

                                                    </td>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if(!$quotation?->quote_status == 'success'): ?>
                                                            <a href="javascript: void(0);"
                                                                wire:click="removeItem(<?php echo e($i); ?>)"><i
                                                                    class="mdi mdi-trash-can text-danger"
                                                                    style="font-size: 25px"></i></a>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

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

                        <div class="form-check mt-2" style="z-index: -9999999999; ">
                            <input class="form-check-input" type="checkbox" wire:model.live="quote_enable_vat"
                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?> id="enableVatCheck">
                            <label class="form-check-label" for="enableVatCheck">
                                คำนวณ VAT 7%
                            </label>
                        </div>

                        

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="clearfix pt-3">
                                    <h6 class="text-muted fs-14">Notes:</h6>
                                    <small>
                                        <textarea wire:model="quote_note" class="form-control" cols="3" rows="3"
                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>></textarea>
                                    </small>

                                </div>
                            </div> <!-- end col -->
                            <div class="col-sm-6">
                                <div class="row">

                                    <div class="col-md-10">
                                        <p><b class="float-end">จำนวนเงินรวม:</b></p>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="float-end"><?php echo e(number_format($quote_subtotal, 2)); ?></span>
                                    </div>
                                    <div class="col-md-10">
                                        <p><b class="float-end">ส่วนลด:</b></p>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="float-end">
                                            <input type="number" wire:model.live.debounce.300ms="quote_discount"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                class="form-control text-end form-control-sm" min="0"
                                                step="0.01">
                                        </span>
                                    </div>
                                    <div class="col-md-10">
                                        <p><b class="float-end">ภาษีมูลค่าเพิ่ม:</b></p>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="float-end"><?php echo e(number_format($quote_vat, 2)); ?></span>
                                    </div>
                                    <div class="col-md-10">
                                        <p><b class="float-end">จำนวนเงินทั้งสิ้น:</b></p>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="float-end"><?php echo e(number_format($quote_grand_total, 2)); ?></span>
                                    </div>

                                </div>

                                <div class="clearfix"></div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row-->


                        <div class="d-print-none mt-4">
                            <div class="text-center">
                                <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                                    <a href="<?php echo e(route('quotations.print', $quotation_id)); ?>" class="btn btn-danger">
                                        <i class="ri-printer-line"></i> Print
                                    </a> &nbsp; &nbsp;
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->



                                <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                                    <button type="submit" class="btn btn-primary"
                                        <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>>
                                        บันทึก
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-info">
                                        สร้างใบเสนอราคา
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


                            </div>
                        </div>
                </form>
                <!-- end buttons -->

            </div> <!-- end card-body-->
        </div> <!-- end card -->
    </div> <!-- end col-->
</div>





<!-- Spinner จะแสดงอัตโนมัติขณะ Livewire ทำงาน -->



<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('quotations.customer-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1200077831-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('quotations.delivery-address-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1200077831-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>


<div>

</div>


<script>
    document.addEventListener("livewire:updated", () => {
        console.log("Livewire updated spinner block");
    });
</script>


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
        $('.select2').select2();

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
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/quotations/quotations-form.blade.php ENDPATH**/ ?>