<div>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    แจ้งชำระเงินสำหรับ Order #<?php echo e($order->order_number); ?>

                </div>
                <div class="card-body">
                    <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if(session('error')): ?>
                        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </ul>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php $stats = $this->getPaymentStats(); ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#f59e42;"><i class="mdi mdi-cash-multiple"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#b45309;">ยอดสุทธิ Order</h6>
                                    <h2 style="font-weight:800; color:#b45309;"><?php echo e(number_format($stats['grand_total'], 2)); ?> <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#bbf7d0 0%,#6ee7b7 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#059669;"><i class="mdi mdi-check-circle-outline"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#059669;">ยอดที่ชำระแล้ว</h6>
                                    <h2 style="font-weight:800; color:#059669;"><?php echo e(number_format($stats['paid'], 2)); ?> <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#fecaca 0%,#fca5a5 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#dc2626;"><i class="mdi mdi-cash-refund"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#dc2626;">ยอดคงค้าง</h6>
                                    <h2 style="font-weight:800; color:#dc2626;"><?php echo e(number_format($stats['outstanding'], 2)); ?> <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        $currentAmount = $mode === 'manual' ? ($manual['amount'] ?? 0) : ($slipData['amount'] ?? 0);
                    ?>
                    <!--[if BLOCK]><![endif]--><?php if(($currentAmount + $paidAmount) > $order->order_grand_total): ?>
                        <div class="alert alert-warning">
                            <strong>แจ้งเตือน:</strong> ยอดที่ชำระครั้งนี้เกินยอดคงค้าง ระบบจะบันทึกเป็นยอดเกินชำระ
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="mode" id="modeApi" wire:model.live="mode" value="api">
                        <label class="btn btn-outline-primary" for="modeApi">ตรวจสอบสลิป</label>
                        <input type="radio" class="btn-check" name="mode" id="modeManual" wire:model.live="mode" value="manual">
                        <label class="btn btn-outline-secondary" for="modeManual">แมนนวลสลิป</label>
                        <input type="radio" class="btn-check" name="mode" id="modePocket" wire:model.live="payment_type" value="pocket_money">
                        <label class="btn btn-outline-success" for="modePocket">Pocket Money</label>
                    </div>
                    
                    <!--[if BLOCK]><![endif]--><?php if($payment_type === 'pocket_money'): ?>
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="mdi mdi-wallet"></i> ยอด Pocket Money คงเหลือ:</strong>
                                    <span class="h5 text-success"><?php echo e(number_format($customerPocketMoney, 2)); ?> บาท</span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($customerPocketMoney <= 0): ?>
                                    <span class="badge bg-danger">ไม่เพียงพอ</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div wire:loading wire:target="slip" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">กำลังตรวจสอบสลิป...</div>
                    </div>
                    <form wire:submit.prevent="submit" enctype="multipart/form-data">
                        <!--[if BLOCK]><![endif]--><?php if($payment_type === 'pocket_money'): ?>
                            
                            <div class="mb-3">
                                <label class="form-label">จำนวนเงินที่ต้องการใช้จาก Pocket Money</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           wire:model.defer="manual.amount" 
                                           placeholder="0.00"
                                           min="1"
                                           max="<?php echo e($customerPocketMoney); ?>"
                                           step="0.01">
                                    <span class="input-group-text">บาท</span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['manual.amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                <small class="text-muted">สูงสุด: <?php echo e(number_format($customerPocketMoney, 2)); ?> บาท</small>
                            </div>
                        <?php else: ?>
                            
                            <div class="mb-3">
                                <label for="slip" class="form-label">แนบสลิปการโอน</label>
                                <input type="file" wire:model="slip" id="slip" accept="image/*" class="form-control">
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
                        <!--[if BLOCK]><![endif]--><?php if($preview): ?>
                            <div class="mb-3">
                                <label class="form-label">สลิปที่อัปโหลด:</label><br>
                                <img src="<?php echo e($preview); ?>" class="img-fluid border" style="max-height: 300px;">
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($mode === 'manual' && $payment_type !== 'pocket_money'): ?>
                            <div class="mb-3">
                                <label>ประเภทการชำระ</label>
                                <select class="form-select" wire:model.live="payment_type">
                                    <option value="transfer">เงินโอน</option>
                                    <option value="cash">รับเงินสด</option>
                                </select>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if(isset($payment_type) && $payment_type == 'cash'): ?>
                                <div class="mb-3">
                                    <label>จำนวนเงิน</label>
                                    <input type="number" class="form-control" wire:model.defer="manual.amount">
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label>จำนวนเงิน</label>
                                    <input type="number" class="form-control" wire:model.defer="manual.amount">
                                </div>
                                <div class="mb-3">
                                    <label>ชื่อผู้โอน</label>
                                    <input type="text" class="form-control" wire:model.defer="manual.sender_name">
                                </div>
                                <div class="mb-3">
                                    <label>ชื่อธนาคาร</label>
                                    <input type="text" class="form-control" wire:model.defer="manual.bank_name">
                                </div>
                                <div class="mb-3">
                                    <label>วันที่โอน</label>
                                    <input type="datetime-local" class="form-control" wire:model.defer="manual.transfer_at">
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php elseif($mode === 'api' && $payment_type !== 'pocket_money'): ?>
                            <!--[if BLOCK]><![endif]--><?php if($slipData): ?>
                                <div class="border p-2 rounded bg-light mb-3">
                                    <div class="mb-2">
                                        <label>จำนวนเงิน</label>
                                        <input type="number" class="form-control" wire:model.defer="manual.amount" value="<?php echo e($slipData['amount'] ?? 0); ?>">
                                    </div>
                                    <p><strong>ชื่อผู้โอน:</strong> <?php echo e($slipData['sender_name'] ?? '-'); ?></p>
                                    <p><strong>ชื่อผู้รับ:</strong> <?php echo e($slipData['receiver_name'] ?? '-'); ?></p>
                                    <p><strong>วันที่โอน:</strong> <?php echo e($slipData['transfer_at'] ?? '-'); ?></p>
                                    <p><strong>ธนาคาร:</strong> <?php echo e($slipData['bank_name'] ?? '-'); ?></p>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-secondary">ย้อนกลับ</a>
                            <button type="submit" 
                                    class="btn <?php echo e($payment_type === 'pocket_money' ? 'btn-success' : 'btn-primary'); ?>"
                                    <?php if($payment_type === 'pocket_money' && $customerPocketMoney <= 0): ?> disabled <?php endif; ?>>
                                <!--[if BLOCK]><![endif]--><?php if($payment_type === 'pocket_money'): ?>
                                    <i class="mdi mdi-wallet me-2"></i>ชำระด้วย Pocket Money
                                <?php else: ?>
                                    บันทึก
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/order-payment-form.blade.php ENDPATH**/ ?>