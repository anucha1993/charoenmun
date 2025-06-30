<div>
    <br>
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#b45309;"><i class="mdi mdi-timer-sand"></i></div>
                    <div style="font-size:1.05rem; color:#b45309; font-weight:600;">รออนุมัติ</div>
                    <div style="font-size:2rem; font-weight:800; color:#b45309;"><?php echo e($stats['pending_count']); ?> <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#b45309;"><?php echo e(number_format($stats['pending_amount'], 2)); ?> บาท</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#059669;"><i class="mdi mdi-check-circle-outline"></i></div>
                    <div style="font-size:1.05rem; color:#059669; font-weight:600;">อนุมัติแล้ว</div>
                    <div style="font-size:2rem; font-weight:800; color:#059669;"><?php echo e($stats['approved_count']); ?> <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#059669;"><?php echo e(number_format($stats['approved_amount'], 2)); ?> บาท</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#dc2626;"><i class="mdi mdi-close-circle-outline"></i></div>
                    <div style="font-size:1.05rem; color:#dc2626; font-weight:600;">ปฏิเสธแล้ว</div>
                    <div style="font-size:2rem; font-weight:800; color:#dc2626;"><?php echo e($stats['rejected_count']); ?> <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#dc2626;">-</div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h4 class="mb-3">รายการขอยืนยันสลิป</h4>
  


    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <table class="table table-bordered table-hover align-middle" style="border-radius:10px; overflow:hidden; background:white;">
        <thead class="table-light" style="font-size:1.05rem;">
            <tr style="background:linear-gradient(135deg,#f8fafc 0%,#e0e7e0 100%);">
                <th>วันที่</th>
                <th>เลขที่ออเดอร์</th>
                <th>ยอดเงิน</th>
                <th>ผู้โอน</th>
                <th>บัญชีปลายทาง</th>
                <th>สลิป</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($payment->transfer_at)->format('d/m/Y H:i')); ?></td>
                    <td><span class="badge bg-info text-dark" style="font-size:1rem;"><?php echo e($payment->order->order_number ?? '-'); ?></span></td>
                    <td><span style="font-weight:700; color:#059669;"><?php echo e(number_format($payment->amount, 2)); ?></span></td>
                    <td><?php echo e($payment->sender_name); ?></td>
                    <td><?php echo e($payment->bank_name); ?></td>
                    <td>
                        <a href="<?php echo e(asset('storage/' . $payment->slip_path)); ?>" target="_blank" class="btn btn-outline-primary btn-sm">ดูสลิป</a>
                    </td>
                    <td>
                        <!--[if BLOCK]><![endif]--><?php if($payment->status === 'รอยืนยันยอด'): ?>
                            <span class="badge bg-warning text-dark"><?php echo e($payment->status); ?></span>
                        <?php elseif($payment->status === 'ชำระเงินแล้ว'): ?>
                            <span class="badge bg-success"><?php echo e($payment->status); ?></span>
                        <?php elseif($payment->status === 'ปฏิเสธ'): ?>
                            <span class="badge bg-danger"><?php echo e($payment->status); ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?php echo e($payment->status); ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm me-1" wire:click="confirm(<?php echo e($payment->id); ?>)"><i class="mdi mdi-check"></i> ยืนยัน</button>
                        <button class="btn btn-danger btn-sm" wire:click="reject(<?php echo e($payment->id); ?>)"><i class="mdi mdi-close"></i> ปฏิเสธ</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">ไม่มีรายการรอยืนยัน</td>
                </tr>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </tbody>
    </table>

          </div>
    </div>

</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/confirm-payments.blade.php ENDPATH**/ ?>