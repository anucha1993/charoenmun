<div>
    

    <?php
        $totalConfirmed = $order->payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
        $totalWaiting = $order->payments->where('status', 'รอยืนยันยอด')->sum('amount');
    ?>
    <div class=" py-3">
        <div class="card shadow-lg border-0"
            style="border-radius: 18px; background: linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);">
            <div class="card-header"
                style="border-radius: 18px 18px 0 0; background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); color: white; box-shadow: 0 2px 8px rgba(102,126,234,0.10);">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 style="font-weight:800; letter-spacing:1px; margin-bottom:0;">Order / ใบสั่งซื้อ</h3>
                        <div style="font-size:16px; color:#e0e7ef;">วันที่:
                            <strong><?php echo e($order->order_date->format('d/m/Y')); ?></strong></div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:16px; color:#fff;">เลขที่: <span
                                style="font-weight:700; color:#3b82f6; background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 100%); padding:6px 12px; border-radius:6px; border:1px solid #93c5fd;"><?php echo e($order->order_number); ?></span>
                        </div>
                        <div style="font-size:16px; color:#a7f3d0;">ชำระเงินแล้ว:
                            <strong><?php echo e(number_format($totalConfirmed)); ?> บาท</strong></div>
                        <div style="font-size:16px; color:#fbbf24;">รอยืนยันยอด:
                            <strong><?php echo e(number_format($totalWaiting)); ?> บาท</strong></div>
                    </div>
                </div>สถานะ
            </div>
            <div class="card-body" style="background: white; border-radius: 0 0 18px 18px;">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="p-4 h-100"
                            style="background:linear-gradient(135deg,#f3f4f6 0%,#e0e7ef 100%); border-radius:12px; box-shadow:0 2px 8px rgba(59,130,246,0.06);">
                            <h5 style="font-weight:700; color:#111827; margin-bottom:8px;"><i
                                    class="ri-user-3-line me-1"></i> ข้อมูลลูกค้า</h5>
                            <div style="font-size:16px; font-weight:700; color:#374151;">
                                <?php echo e($order->customer->customer_name); ?></div>
                            <div style="font-size:14px; color:#6b7280;"><?php echo e($order->customer->customer_address); ?></div>
                            <div style="font-size:13px; color:#9ca3af;"><?php echo e($order->customer->customer_district_name); ?>

                                <?php echo e($order->customer->customer_amphur_name); ?>

                                <?php echo e($order->customer->customer_province_name); ?> <?php echo e($order->customer->customer_zipcode); ?>

                            </div>
                            <div style="font-size:14px; color:#6b7280; margin-top:4px;"><i class="ri-phone-line"></i>
                                (+66) <?php echo e($order->customer->customer_phone); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="p-4 h-100"
                            style="background:linear-gradient(135deg,#f3f4f6 0%,#e0e7ef 100%); border-radius:12px; box-shadow:0 2px 8px rgba(59,130,246,0.06);">
                            <h5 style="font-weight:700; color:#111827; margin-bottom:8px;"><i
                                    class="ri-map-pin-line me-1"></i> ที่อยู่จัดส่ง</h5>
                            <!--[if BLOCK]><![endif]--><?php if($order->deliveryAddress): ?>
                                <div style="font-size:16px; font-weight:700; color:#059669;">
                                    <?php echo e($order->deliveryAddress->delivery_contact_name); ?> <span
                                        style="color:#6b7280;">(<?php echo e($order->deliveryAddress->delivery_phone); ?>)</span>
                                </div>
                                <div style="font-size:14px; color:#374151;">
                                    <?php echo e($order->deliveryAddress->delivery_number); ?></div>
                                <div style="font-size:13px; color:#9ca3af;">
                                    <?php echo e($order->deliveryAddress->delivery_address); ?></div>
                            <?php else: ?>
                                <div style="font-size:16px; color:#374151;"><?php echo e($order->customer->customer_name); ?></div>
                                <div style="font-size:14px; color:#6b7280;"><?php echo e($order->customer->customer_address); ?>

                                </div>
                                <div style="font-size:13px; color:#9ca3af;">
                                    <?php echo e($order->customer->customer_district_name); ?>

                                    <?php echo e($order->customer->customer_amphur_name); ?>

                                    <?php echo e($order->customer->customer_province_name); ?>

                                    <?php echo e($order->customer->customer_zipcode); ?></div>
                                <div style="font-size:14px; color:#6b7280; margin-top:4px;"><i
                                        class="ri-phone-line"></i> (+66) <?php echo e($order->customer->customer_phone); ?></div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="p-3 h-100 d-flex flex-wrap align-items-center gap-2"
                            style="background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%); border-radius:12px; box-shadow:0 2px 8px rgba(59,130,246,0.04); font-size:15px;">
                            <div class="mb-2 d-flex flex-wrap align-items-center gap-2">
                                <span><b>สถานะ:</b> <?php echo order_status_badge($order->order_status); ?></span>
                                <button class="btn btn-sm btn-outline-info" wire:click="checkDeliveryStatus" title="ตรวจสอบและอัพเดทสถานะการจัดส่ง">
                                    <i class="ri-refresh-line"></i>
                                </button>
                                
                                <span><b>ชำระเงิน:</b> <?php echo payment_status_badge($order->payment_status); ?></span>
                            </div>
                            <div class="mb-2 d-flex flex-wrap align-items-center gap-2">
                                <span><b>ยอดรวมก่อนหักส่วนลด:</b> <span class="text-dark"><?php echo e(number_format($order_subtotal_before_discount, 2)); ?></span> บาท
                                </span>
                                <span><b>ส่วนลด:</b> <span class="text-danger"><?php echo e(number_format($order_discount, 2)); ?></span>
                                    บาท</span>
                                <span><b>ยอดสุทธิหลังหักส่วนลด:</b> <span class="text-primary"><?php echo e(number_format($order_subtotal, 2)); ?></span> บาท</span>
                                <span><b>ภาษีมูลค่าเพิ่ม (VAT 7%):</b> <span class="text-primary"><?php echo e(number_format($order_vat, 2)); ?></span> บาท
                                </span>
                                
                                <span style="font-weight:700; color:#059669;"><b>จำนวนเงินทั้งสิ้น:</b> <span
                                        class="text-success"><?php echo e(number_format($order_grand_total, 2)); ?></span> บาท</span>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <div class="p-3 h-100 d-flex flex-wrap align-items-center gap-2"
                            style="background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%); border-radius:12px; box-shadow:0 2px 8px rgba(59,130,246,0.04); font-size:15px;">

                            <div class="mb-2 d-flex flex-wrap align-items-center gap-2">
                                <span style="color:#059669;"><b>ชำระเงินแล้ว:</b> <span
                                        style="color:#059669; font-weight:700;"><?php echo e(number_format($totalConfirmed)); ?>

                                        บาท</span></span>
                                
                                <span style="color:#fbbf24;"><b>รอยืนยันยอด:</b> <span
                                        style="color:#f59e42; font-weight:700;"><?php echo e(number_format($totalWaiting)); ?>

                                        บาท</span></span>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if(
                                $order->payment_status === 'pending' ||
                                    $order->payment_status === 'partial' ||
                                    $order->payment_status === 'waiting_confirmation'): ?>
                                <a href="<?php echo e(route('orders.payment.livewire', $order->id)); ?>"
                                    class="btn btn-sm btn-success mt-2">
                                    แจ้งชำระเงิน
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="col-md-6 text-end align-self-end d-flex align-items-end justify-content-end">
                        <!--[if BLOCK]><![endif]--><?php if($order->status === 'open'): ?>
                            <button wire:click="approveOrder" class="btn btn-primary mt-2"><i class="ri-check-line"></i>
                                อนุมัติ Order</button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($showPaymentForm): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card border-success mb-3" style="max-width: 600px; margin:auto;">
                                <div class="card-header bg-success text-white">แจ้งชำระเงินสำหรับ Order
                                    #<?php echo e($order->order_number); ?>

                                    <button type="button" class="btn-close float-end" aria-label="Close"
                                        wire:click="$set('showPaymentForm', false)"></button>
                                </div>
                                <div class="card-body">
                                    <form wire:submit.prevent="submitPayment">
                                        <div class="mb-2">
                                            <label>แนบสลิป</label>
                                            <input type="file" class="form-control" wire:model="slip">
                                            <!--[if BLOCK]><![endif]--><?php if($slip): ?>
                                                <img src="<?php echo e($slip->temporaryUrl()); ?>" class="img-thumbnail mt-2"
                                                    style="max-width:200px;">
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['slip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-danger"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="mb-2">
                                            <label>จำนวนเงิน</label>
                                            <input type="number" class="form-control" wire:model.defer="amount">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-danger"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="mb-2">
                                            <label>ชื่อผู้โอน</label>
                                            <input type="text" class="form-control" wire:model.defer="sender_name">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['sender_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-danger"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="mb-2">
                                            <label>วันที่โอน</label>
                                            <input type="datetime-local" class="form-control"
                                                wire:model.defer="transfer_at">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['transfer_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-danger"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <button type="submit" class="btn btn-success">บันทึก</button>
                                        <button type="button" class="btn btn-secondary ms-2"
                                            wire:click="$set('showPaymentForm', false)">ยกเลิก</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover"
                                style="background:white; border-radius:8px; overflow:hidden;">
                                <thead class="table-light">
                                    <tr style="background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);">
                                        <th>#</th>
                                        <th>สินค้า</th>
                                        <th>รายละเอียด</th>
                                        <th>จำนวนสั่ง</th>
                                        <th>จำนวนที่จัดส่งแล้ว</th>
                                        <th>หน่วย</th>
                                        <th>ความหนา</th>
                                        <th>ความยาว:เมตร</th>
                                        <th>ราคา/หน่วย</th>
                                        <th>VAT</th>
                                        <th>เหตุผล</th>
                                        <th>หมายเหตุ</th>
                                        <th class="text-end">ยอดรวม</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($idx + 1); ?></td>
                                            <td><b><?php echo e($item->product_name); ?></b><br><span
                                                    style="color:#9ca3af;"><?php echo e($item->product_note); ?></span></td>
                                            <td><?php echo e($item->product_detail); ?></td>
                                            <td><?php echo e($item->quantity); ?></td>
                                            <td><?php echo e($item->delivered_qty ?? ($deliveredQtyMap[$item->id] ?? 0)); ?></td>
                                            <td><?php echo e($item->product_unit); ?></td>
                                            <td><?php echo e($item->product_calculation !== 1 ? $item->product_calculation : 0); ?></td>
                                            <td><?php echo e($item->product_length); ?></td>
                                            <td><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                            <td class="text-center">
                                                <!--[if BLOCK]><![endif]--><?php if($item->product_vat): ?>
                                                    <span class="badge bg-success">มี VAT</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                            <td>
                                                <!--[if BLOCK]><![endif]--><?php if($item->added_reason === 'claim'): ?>
                                                    <span class="badge bg-warning text-dark">เคลม</span>
                                                <?php elseif($item->added_reason === 'customer_request'): ?>
                                                    <span class="badge bg-info text-dark">เพิ่มตามคำขอ</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">-</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                            <td><?php echo e($item->added_note ?? '-'); ?></td>
                                            <td class="text-end">
                                                <?php
                                                    $qty = (float)($item->quantity ?? 0);
                                                    $unit = (float)($item->unit_price ?? 0);
                                                    $calc = (isset($item->product_calculation) && $item->product_calculation !== '' && $item->product_calculation !== null) ? (float)$item->product_calculation : 1;
                                                    $len = (isset($item->product_length) && $item->product_length !== '' && $item->product_length !== null) ? (float)$item->product_length : 1;
                                                    // สูตรที่ถูกต้อง: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
                                                    $rowSubtotal = $unit * $calc * $len * $qty;
                                                    $rowVat = (!empty($item->product_vat)) ? round($rowSubtotal * 0.07, 2) : 0;
                                                    $rowTotal = $rowSubtotal + $rowVat;
                                                ?>
                                                <div><?php echo e(number_format($rowSubtotal, 2)); ?></div>
                                               
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="deleteOrderItem(<?php echo e($item->id); ?>)"
                                                    onclick="return confirm('ยืนยันการลบรายการนี้?')">ลบ</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <button class="btn btn-outline-primary btn-sm" wire:click="addRow">+
                                    เพิ่มสินค้าใหม่</button>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if(count($newItems) > 0): ?>
                                <form wire:submit.prevent="saveNewItems">
                                    <table class="table table-bordered table-sm mt-2">
                                        <thead>
                                            <tr>
                                                <th>สินค้า</th>
                                                <th>จำนวน</th>
                                                <th>ความยาว</th>
                                                <th>ราคา/หน่วย</th>
                                                <th>VAT</th>
                                                <th>เหตุผลการเพิ่ม</th>
                                                <th>หมายเหตุ</th>
                                                <th>ราคารวม</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $newItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td style="min-width:100px;">
                                                        <div class="product-search-container">
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model.live="newItems.<?php echo e($idx); ?>.product_search"
                                                                placeholder="ค้นหาสินค้า..."
                                                                wire:focus="$set('newItems.<?php echo e($idx); ?>.product_results_visible', true)"
                                                                wire:keydown.escape="$set('newItems.<?php echo e($idx); ?>.product_results_visible', false)">
                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($item['product_results_visible']) && !empty($item['product_results'])): ?>
                                                                <div class="position-absolute w-100 mt-1"
                                                                    style="z-index: 1000;">
                                                                    <div class="list-group shadow rounded"
                                                                        style="max-height: 300px; overflow-y: auto;">
                                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['product_results']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <a href="javascript: void(0);"
                                                                                class="list-group-item list-group-item-action"
                                                                                wire:click="selectProductForNewItem(<?php echo e($idx); ?>, <?php echo e($result->product_id); ?>)">
                                                                                <div
                                                                                    class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 class="mb-1">
                                                                                            <?php echo e($result->product_name); ?>

                                                                                        </h6>
                                                                                        <small
                                                                                            class="text-muted"><?php echo e($result->product_size); ?>

                                                                                            |
                                                                                            <?php echo e($result->productWireType?->value ?? '-'); ?></small>
                                                                                    </div>
                                                                                    <i
                                                                                        class="ri-arrow-right-s-line"></i>
                                                                                </div>
                                                                            </a>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                    </div>


                                                                </div>
                                                                
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($item['product_calculation']) && $item['product_calculation'] != 1): ?>
                                                                <input type="number" step="0.01"
                                                                    class="form-control form-control-sm mt-1"
                                                                    wire:model.live="newItems.<?php echo e($idx); ?>.product_calculation"
                                                                    placeholder="ความหนา" />
                                                            <?php else: ?>
                                                                <div class="text-muted small text-center">
                                                                    <?php echo $item['product_detail'] ?? '-'; ?>

                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                        <!--[if BLOCK]><![endif]--><?php if($item['selected_from_dropdown']): ?>
                                                            <span class="badge bg-success mt-1">เลือกแล้ว</span>
                                                            <button type="button"
                                                                class="btn btn-link btn-sm p-0 text-danger"
                                                                wire:click="clearProductSelectionForNewItem(<?php echo e($idx); ?>)">ล้าง</button>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td><input type="number" min="1"
                                                            wire:model.live="newItems.<?php echo e($idx); ?>.quantity"
                                                            class="form-control form-control-sm"></td>
                                                    <td><input type="number" min="0" step="0.01"
                                                            wire:model.live="newItems.<?php echo e($idx); ?>.product_length"
                                                            class="form-control form-control-sm" placeholder="ความยาว"></td>
                                                    <td><input type="number" min="0" step="0.01"
                                                            wire:model.live="newItems.<?php echo e($idx); ?>.unit_price"
                                                            class="form-control form-control-sm"></td>
                                                    <td class="text-center"><input type="checkbox"
                                                            wire:model.live="newItems.<?php echo e($idx); ?>.product_vat">
                                                    </td>
                                                    <td>
                                                        <select wire:model.live="newItems.<?php echo e($idx); ?>.added_reason"
                                                            class="form-control form-control-sm">
                                                            <option value="">เลือกเหตุผล</option>
                                                            <option value="customer_request">เพิ่มตามคำขอลูกค้า
                                                            </option>
                                                            <option value="claim">เพิ่มกรณีเคลม</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text"
                                                            wire:model="newItems.<?php echo e($idx); ?>.added_note"
                                                            class="form-control form-control-sm" placeholder="หมายเหตุ"></td>
                                                    <td class="text-end align-middle">
                                                        <?php
                                                            $qty = (float)($item['quantity'] ?? 0);
                                                            $unit = (float)($item['unit_price'] ?? 0);
                                                            $calc = (isset($item['product_calculation']) && $item['product_calculation'] !== '' && $item['product_calculation'] !== null) ? (float)$item['product_calculation'] : 1;
                                                            $len = (isset($item['product_length']) && $item['product_length'] !== '' && $item['product_length'] !== null) ? (float)$item['product_length'] : 1;
                                                            // สูตรที่ถูกต้อง: ราคา/หน่วย × ความหนา × ความยาว × จำนวน
                                                            $total = $unit * $calc * $len * $qty;
                                                            // Debug information to verify calculation
                                                            $debug = "Unit: {$unit}, Calc: {$calc}, Len: {$len}, Qty: {$qty}, Total: {$total}";
                                                        ?>
                                                        <span title="<?php echo e($debug); ?>"><?php echo e(number_format($total, 2)); ?></span>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            wire:click="removeRow(<?php echo e($idx); ?>)">ลบ</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                    <div class="text-end">
                                        <button type="submit"
                                            class="btn btn-success btn-sm">บันทึกสินค้าใหม่</button>
                                    </div>
                                </form>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 mb-2">
                        <h5 style="font-weight:700; color:#111827;"><i class="ri-truck-line me-1"></i> รายการจัดส่ง
                            (Order Deliveries)</h5>
                    </div>
                    
                    
                    
                    
                    <div class="col-12 mb-3">
                        <div class="card border-secondary"
                            style="border-radius:10px; box-shadow:0 2px 8px rgba(59,130,246,0.04);">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover mb-0"
                                        style="font-size:14px; background:white; border-radius:8px; overflow:hidden;">
                                        <thead>
                                            <tr style="background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);">
                                                <th>ลำดับ</th>
                                                <th>วันที่จัดส่ง</th>
                                                <th>เลขที่บิลย่อย</th>
                                                <th>น้ำหนักรวม</th>
                                                <th><i class="ri-truck-line me-1"></i>ประเภทรถ</th>
                                                <th>จำนวนเงินทั้งสิ้น</th>
                                                <th>สถานะจัดส่ง</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $order->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($key + 1); ?></td>
                                                    <td><?php echo e($delivery->order_delivery_date->format('d/m/Y')); ?></td>
                                                    <td><?php echo e($delivery->order_delivery_number); ?></td>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if($delivery->total_weight_kg > 0): ?>
                                                            <div class="d-flex align-items-center">
                                                                <i class="ri-weight-line me-2 text-muted"></i>
                                                                <span class="fw-bold"><?php echo weight_display($delivery->total_weight_kg); ?></span>
                                                            </div>
                                                            <!--[if BLOCK]><![endif]--><?php if($delivery->isOverweight()): ?>
                                                                <small class="text-danger">
                                                                    <i class="ri-alert-line"></i> เกินขีดจำกัด
                                                                </small>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php else: ?>
                                                            <span class="text-muted">ไม่ระบุ</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td>
                                                        <div class="truck-info">
                                                           
                                                            <!--[if BLOCK]><![endif]--><?php if($delivery->selected_truck_type): ?>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2" style="font-size: 1.2em;">
                                                                        <?php echo e(truck_type_icon($delivery->selected_truck_type)); ?>

                                                                    </span>
                                                                    <div>
                                                                        <small class="text-muted">เลือกใช้:</small>
                                                                        <?php echo truck_type_badge($delivery->selected_truck_type); ?>

                                                                        <!--[if BLOCK]><![endif]--><?php if($delivery->total_weight_kg > 0): ?>
                                                                            <?php echo weight_status_badge($delivery->total_weight_kg, $delivery->selected_truck_type); ?>

                                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted">ไม่ได้เลือก</span>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            <!--[if BLOCK]><![endif]--><?php if($delivery->calculateRequiredTrips() > 1): ?>
                                                                <div class="mt-1">
                                                                    <small class="badge bg-warning">
                                                                        <i class="ri-truck-line me-1"></i>
                                                                        ต้องใช้ <?php echo e($delivery->calculateRequiredTrips()); ?> รอบ
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </td>
                                                    <td><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?>

                                                    </td>
                                                    <td><?php echo order_delivery_status_badge($delivery->order_delivery_status); ?></td>
                                                    <td style="font-size: 18px">

                                                        <a href="<?php echo e(route('deliveries.printer', $delivery->id)); ?>"
                                                            class="text-pink" title="พิมพ์"><i
                                                                class="mdi mdi-printer"></i></a> |
                                                        <a href="javascript:void(0)" 
                                                            class="text-info"
                                                            wire:click="showDeliveryPrintHistory(<?php echo e($delivery->id); ?>)"
                                                            title="ประวัติการพิมพ์"><i
                                                                class="mdi mdi-history"></i></a> |
                                                        <a href="<?php echo e(route('deliveries.edit', [$delivery->order_id, $delivery->id])); ?>"
                                                            class="text-dark" title="แก้ไข"><i
                                                                class="mdi mdi-content-save-edit-outline"></i></a> |
                                                        <button type="button"
                                                            class="btn btn-link text-danger p-0 m-0 align-baseline"
                                                            style="font-size:18px;"
                                                            wire:click="deleteDelivery(<?php echo e($delivery->id); ?>)"
                                                            onclick="return confirm('ยืนยันการลบรายการจัดส่งนี้?')"
                                                            title="ลบ"><i class="mdi mdi-trash-can"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($order->order_status === 'open'): ?>
                        <div class="col-12">
                            <button wire:click="createNewDelivery" class="btn btn-primary">
                                <i class="ri-truck-line"></i> สร้างรอบจัดส่งใหม่
                            </button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>


    <!-- Modal สำหรับเลือกหน้า -->
    <div class="modal fade" id="printPriceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เลือกสำเนาที่ต้องการแสดงราคา</h5>
                </div>
                <div class="modal-body">
                    <form id="priceSelectionForm" method="GET">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="0" id="showPrice0">
                            <label class="form-check-label" for="showPrice0">หน้า 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="showPrice1">
                            <label class="form-check-label" for="showPrice1">หน้า 2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="2" id="showPrice2">
                            <label class="form-check-label" for="showPrice2">หน้า 3</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" disabled checked>
                            <label class="form-check-label">หน้า 4 (แสดงราคาเสมอ)</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="printWithPriceBtn"
                        onclick="applyPriceAndRedirect()">พิมพ์เอกสาร</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal ประวัติการพิมพ์ -->
    <div class="modal fade <?php echo e($showPrintHistory ? 'show' : ''); ?>" id="printHistoryModal" tabindex="-1" role="dialog" 
        style="<?php echo e($showPrintHistory ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;'); ?>">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ประวัติการพิมพ์ใบส่งของ</h5>
                    <button type="button" class="btn-close" wire:click="closePrintHistory"></button>
                </div>
                <div class="modal-body">
                    <!--[if BLOCK]><![endif]--><?php if(count($printHistory) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ครั้งที่</th>
                                        <th>ผู้พิมพ์</th>
                                        <th>วันที่พิมพ์</th>
                                        <th>สถานะการส่ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $printHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($history['print_count']); ?></td>
                                            <td><?php echo e($history['printed_by']); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($history['printed_at'])->format('d/m/Y H:i:s')); ?></td>
                                            <td>
                                                <!--[if BLOCK]><![endif]--><?php if($history['is_complete_delivery']): ?>
                                                    <span class="badge bg-success">ส่งของครบ</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">ส่งของบางส่วน</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            ไม่พบประวัติการพิมพ์ใบส่งของนี้
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closePrintHistory">ปิด</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDeliveryId = null;
    
    function openPrintPreview(deliveryId) {
        currentDeliveryId = deliveryId;
        const selected = [];
        for (let i = 0; i <= 2; i++) {
            const checkbox = document.getElementById('showPrice' + i);
            if (checkbox && checkbox.checked) {
                selected.push(i);
            }
        }
        // สร้าง query string เช่น show_price[]=0&show_price[]=1
        const query = selected.map(i => `show_price[]=${encodeURIComponent(i)}`).join('&');
        // สร้าง URL ไปยัง route delivery/print
        const printUrl = `<?php echo e(url('deliveries')); ?>/${deliveryId}/print?${query}`;
        // เปิดในแท็บใหม่
        window.open(printUrl, '_blank');
        // ปิด modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('printPriceModal'));
        if (modal) modal.hide();
    }

    function applyPriceAndRedirect() {
        if (!currentDeliveryId) {
            alert('กรุณาเลือกบิลย่อยก่อน');
            return;
        }
        
        const selected = [];
        for (let i = 0; i <= 2; i++) {
            const checkbox = document.getElementById('showPrice' + i);
            if (checkbox && checkbox.checked) {
                selected.push(i);
            }
        }
        
        // สร้าง query string เช่น show_price[]=0&show_price[]=1
        const query = selected.map(i => `show_price[]=${encodeURIComponent(i)}`).join('&');
        
        // สร้าง URL ไปยัง route delivery/print
        const printUrl = `<?php echo e(url('deliveries')); ?>/${currentDeliveryId}/print?${query}`;
        
        // เปิดในแท็บใหม่
        window.open(printUrl, '_blank');
        
        // ปิด modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('printPriceModal'));
        if (modal) modal.hide();
    }
</script>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/order-show.blade.php ENDPATH**/ ?>