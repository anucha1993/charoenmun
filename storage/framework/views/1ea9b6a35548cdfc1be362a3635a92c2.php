<div>
    <div class="container py-3">
        <div class="card shadow-lg border-0"
            style="border-radius: 18px; background: linear-gradient(135deg,#fdf6e3 0%,#f1f5f9 100%);">
            <div class="card-header"
                style="border-radius: 18px 18px 0 0; background: linear-gradient(135deg,#fbbf24 0%,#f59e42 100%); color: #fff; box-shadow: 0 2px 8px rgba(251,191,36,0.10);">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <img src="/images/logo-crrtm.png" alt="logo" height="60" class="me-2">
                        <div>
                            <h3
                                style="font-weight:800; letter-spacing:1px; margin-bottom:0; color:#fff;">
                                Delivery Order / ใบส่งสินค้า
                            </h3>
                            <div style="font-size:15px; color:#fef9c3;">บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)
                            </div>
                            <div style="font-size:13px; color:#fef9c3;">ที่อยู่ 99/35 หมู่ 9 ต.ละหาร อ.บางบัวทอง จ.นนทบุรี
                                11110 โทร 082-4789197 เลขประจำตัวผู้เสียภาษี 0125560015546
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="input-group flex-nowrap mb-2">
                            <span class="input-group-text bg-warning text-dark border-0">เลขที่บิลหลัก</span>
                            <input type="text" class="form-control col-form-label-lg bg-light"
                                value="<?php echo e($orderModel->order_number); ?>" disabled>
                        </div>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text bg-warning text-dark border-0">วันที่จัดส่ง</span>
                            <input type="date" class="form-control col-form-label-lg bg-light" required
                                wire:model="order_delivery_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="background: white; border-radius: 0 0 18px 18px;">
                <form wire:submit="saveDelivery" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <div class="p-4 h-100"
                                style="background:linear-gradient(135deg,#fef9c3 0%,#fbbf24 100%); border-radius:12px; box-shadow:0 2px 8px rgba(251,191,36,0.06);">
                                <h5
                                    style="font-weight:700; color:#b45309; margin-bottom:8px;">
                                    <i class="ri-user-3-line me-1"></i> ข้อมูลลูกค้า
                                </h5>
                                <div
                                    style="font-size:16px; font-weight:700; color:#374151;">
                                    <?php echo e($orderModel->customer->customer_name); ?>

                                </div>
                                <div style="font-size:14px; color:#6b7280;">
                                    <?php echo e($orderModel->customer->customer_address); ?>

                                </div>
                                <div style="font-size:13px; color:#9ca3af;">

                                    <?php echo e($orderModel->customer->customer_district_name); ?>

                                    <?php echo e($orderModel->customer->customer_amphur_name); ?>

                                    <?php echo e($orderModel->customer->customer_province_name); ?>

                                    <?php echo e($orderModel->customer->customer_zipcode); ?>

                                    
                                </div>
                                <div style="font-size:14px; color:#6b7280; margin-top:4px;">
                                    <i class="ri-phone-line"></i> (+66) <?php echo e($orderModel->customer->customer_phone); ?>

                                </div>
                                <div style="font-size:13px; color:#b45309; margin-top:4px;">
                                    เลขประจำตัวผู้เสียภาษี: <?php echo e($orderModel->customer->customer_taxid); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-4 h-100"
                                style="background:linear-gradient(135deg,#fef9c3 0%,#fbbf24 100%); border-radius:12px; box-shadow:0 2px 8px rgba(251,191,36,0.06);">
                                <h5
                                    style="font-weight:700; color:#b45309; margin-bottom:8px;">
                                    <i class="ri-map-pin-line me-1"></i> ข้อมูลจัดส่ง
                                </h5>
                                <div>
                                    <a href="#"
                                        wire:click.prevent="openDeliveryModal(<?php echo e($orderModel->customer->id); ?>)"
                                        style="font-size:13px; color:#b45309;">+ เพิ่มที่อยู่จัดส่ง</a>
                                </div>
                                <select wire:model.live="selected_delivery_id" name="selected_delivery_id"
                                    class="form-select mt-2">
                                    <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($delivery->id); ?>" wire:key="delivery-<?php echo e($delivery->id); ?>"
                                        <?php if($delivery->id == (string) $selected_delivery_id): echo 'selected'; endif; ?>>
                                        <?php echo e($delivery->delivery_contact_name); ?> - <?php echo e($delivery->delivery_phone); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <address class="mt-2">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedDelivery): ?>
                                    <b>ชื่อผู้ติดต่อ</b> <?php echo e($selectedDelivery->delivery_contact_name); ?>

                                    (<?php echo e($selectedDelivery->delivery_phone); ?>)<br>
                                    <b>ที่อยู่:</b> <?php echo e($selectedDelivery->delivery_number); ?><br>
                                    <?php echo e($selectedDelivery->delivery_address); ?>

                                    <a href="javascript: void(0);"
                                        onclick="Livewire.dispatch('edit-delivery-modal', { deliveryId: <?php echo e($selectedDelivery->id); ?> })"
                                        style="font-size:13px; color:#b45309;">แก้ไข</a>
                                    <?php else: ?>
                                    <!--[if BLOCK]><![endif]--><?php if($orderModel->customer->customer_name): ?>
                                    <b>ชื่อร้านค้า/ชื่อลูกค้า:</b> <?php echo e($orderModel->customer->customer_contract_name); ?>

                                    (<?php echo e($orderModel->customer->customer_phone); ?>)<br>
                                    <b>ที่อยู่:</b> <?php echo e($orderModel->customer->customer_address); ?>

                                    <?php echo e($orderModel->customer->customer_district_name); ?>

                                    <?php echo e($orderModel->customer->customer_amphur_name); ?>

                                    <?php echo e($orderModel->customer->customer_province_name); ?>

                                    <?php echo e($orderModel->customer->customer_zipcode); ?><br>
                                    <b>เลขประจำตัวผู้เสียภาษี:</b> <?php echo e($orderModel->customer->customer_taxid); ?>

                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3"
                                    style="background:white; border-radius:8px; overflow:hidden;">
                                    <thead class="border-top border-bottom bg-light-subtle border-light">
                                        <tr style="background:linear-gradient(135deg,#fef9c3 0%,#fbbf24 100%);">
                                            <th>#</th>
                                            <th>รายการสินค้า</th>
                                            <th>รายละเอียดสินค้า</th>
                                            <th>ความยาว</th>
                                            <th>น้ำหนัก</th>
                                            <th>จำนวน</th>
                                            <th>หน่วยนับ</th>
                                            <th>ราคา/หน่วย</th>
                                            <th class="text-end">รวมทั้งสิ้น</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="align-top" wire:key="row-<?php echo e($i); ?>">
                                            <td class="align-top"><?php echo e($i + 1); ?></td>
                                            <td style="min-width: 250px;">
                                                <select class="form-select form-select-sm"
                                                    wire:model.live="items.<?php echo e($i); ?>.product_id">
                                                    <option value="">-- เลือกสินค้า --</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $left = $stocksLeft[$oi->product_id] ?? 0; ?>
                                                    <option value="<?php echo e($oi->product_id); ?>" <?php if($left === 0): echo 'disabled'; endif; ?>>
                                                        <?php echo e($oi->product->product_name); ?>ขนาด<?php echo e($oi->product->product_length); ?> เมตร
                                                        (<?php echo e($left); ?>) <?php echo e($oi->product_calculation); ?>

                                                    </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <div class="mt-2">
                                                    <input type="text" class="form-control form-control-sm"
                                                        wire:model="items.<?php echo e($i); ?>.product_note"
                                                        placeholder="💬 หมายเหตุ"
                                                        style="background-color: #f8f9fa; border: 1px solid #fbbf24;">
                                                </div>
                                            </td>
                                            <td style="min-width: 180px;"><?php echo $item['product_detail'] ?? ''; ?></td>
                                            <td style="width: 90px">
                                                <input type="text"
                                                    wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.product_length"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 90px">
                                                <input type="number" min="1"
                                                    wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.product_weight"
                                                    class="form-control form-control-sm" />
                                            </td>
                                            <td style="width: 90px">
                                                <input type="number" min="1"
                                                    wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.quantity"
                                                    class="form-control form-control-sm" />
                                            </td>
                                            <td style="width: 90px">
                                                <input type="text"
                                                    wire:model.live="items.<?php echo e($i); ?>.product_unit"
                                                    class="form-control form-control-sm"
                                                    style="background-color: aliceblue" readonly>
                                            </td>
                                            <td style="width: 120px">
                                                <input type="number" min="0" step="0.01" readonly
                                                    style="background-color: aliceblue"
                                                    wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.unit_price"
                                                    class="form-control form-control-sm text-end" />
                                            </td>
                                            <td class="text-end">
                                                <?php echo e(number_format($item['total'], 2)); ?>

                                            </td>
                                            <td>
                                                <a href="javascript: void(0);"
                                                    wire:click="removeItem(<?php echo e($i); ?>)"><i
                                                        class="mdi mdi-trash-can text-danger"
                                                        style="font-size: 22px"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-outline-warning btn-sm mt-2"
                                    wire:click="addEmptyItem">
                                    ➕ เพิ่มรายการสินค้า
                                </button>
                                <!--[if BLOCK]><![endif]--><?php if(!$editing): ?>
                                <button type="button" class="btn btn-outline-info btn-sm mt-2 ms-2"
                                    wire:click="resetToAllItems">
                                    🔄 รีเซ็ตเป็นรายการทั้งหมด
                                </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted fs-14">Notes:</h6>
                                <small>
                                    <textarea wire:model="order_delivery_note" class="form-control" cols="3"
                                        rows="3"></textarea>
                                </small>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" wire:model.live="order_delivery_enable_vat"
                                    id="enableVatCheck">
                                <label class="form-check-label" for="enableVatCheck">
                                    คำนวณ VAT 7%
                                </label>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($order_delivery_enable_vat): ?>
                            <div class="form-check mt-2 ms-3">
                                <input class="form-check-input" type="checkbox" wire:model.live="order_delivery_vat_included"
                                    id="vatIncludedCheck">
                                <label class="form-check-label" for="vatIncludedCheck">
                                    💡 คิดรวม VAT ในราคารวม (VAT-In)
                                </label>
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-8 text-end"><b>จำนวนเงินรวม:</b></div>
                                <div class="col-4 text-end"><?php echo e(number_format($order_delivery_subtotal, 2)); ?></div>
                                <div class="col-8 text-end"><b>ส่วนลด:</b></div>
                                <div class="col-4 text-end">
                                    <input type="number" wire:model.live.debounce.300ms="order_delivery_discount"
                                        class="form-control text-end form-control-sm" min="0" step="0.01">
                                </div>
                                <div class="col-8 text-end"><b>ภาษีมูลค่าเพิ่ม:</b></div>
                                <div class="col-4 text-end"><?php echo e(number_format($order_delivery_vat, 2)); ?></div>
                                <div class="col-8 text-end"><b>จำนวนเงินทั้งสิ้น:</b></div>
                                <div class="col-4 text-end">
                                    <span style="font-weight:700; color:#b45309;">
                                        <?php echo e(number_format($order_delivery_grand_total, 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-print-none mt-4">
                        <div class="text-center">
                            <button type="submit"
                                class="btn <?php echo e($editing ? 'btn-success' : 'btn-warning'); ?> px-4 py-2"
                                style="font-weight:600; font-size:1.1rem;"
                                <?php if(empty($selected_delivery_id)): ?> disabled <?php endif; ?>
                            >
                                <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                    <i class="ri-save-line me-1"></i> บันทึกการแก้ไข
                                <?php else: ?>
                                    <i class="ri-file-list-3-line me-1"></i> สร้างใบส่งสินค้า
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </button>
                            <!--[if BLOCK]><![endif]--><?php if(empty($selected_delivery_id)): ?>
                                <div class="text-danger mt-2">กรุณาเลือกที่อยู่จัดส่งก่อน<?php echo e($editing ? 'บันทึก' : 'สร้างใบส่งสินค้า'); ?></div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('quotations.customer-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-2759676399-0', $__slots ?? [], get_defined_vars());

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

$__html = app('livewire')->mount($__name, $__params, 'lw-2759676399-1', $__slots ?? [], get_defined_vars());

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
    window.addEventListener('qty-over', e => {
        alert(`จำนวนของ ${e.detail.name} เกินคงเหลือ (สูงสุด ${e.detail.max})`);
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
</div>
<?php /**PATH /Users/ap.dev/Desktop/Projects/charoenmun/resources/views/livewire/orders/order-delivery.blade.php ENDPATH**/ ?>