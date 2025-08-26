<div class="container-fluid py-3">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">🔍 ตัวกรอง</h5>
                    
                    <div class="list-group mb-4">
                        <button class="list-group-item list-group-item-action <?php echo e($filterType === 'today' ? 'active' : ''); ?>"
                            wire:click="setFilter('today')">
                            <i class="ri-calendar-todo-fill"></i> รายการวันนี้
                        </button>
                        <button class="list-group-item list-group-item-action <?php echo e($filterType === 'pending' ? 'active' : ''); ?>"
                            wire:click="setFilter('pending')">
                            <i class="ri-error-warning-fill"></i> รายการค้างตรวจสอบ
                        </button>
                        <button class="list-group-item list-group-item-action <?php echo e($filterType === 'date-range' ? 'active' : ''); ?>"
                            wire:click="setFilter('date-range')">
                            <i class="ri-calendar-check-fill"></i> เลือกช่วงวันที่
                        </button>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($filterType === 'date-range'): ?>
                    <div class="mb-3">
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" wire:model.live="startDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" wire:model.live="endDate">
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="mb-3">
                        <label class="form-label">สถานะการจัดส่ง</label>
                        <select class="form-select" wire:model.live="deliveryStatus">
                            <option value="">ทั้งหมด</option>
                            <option value="pending">รอจัดส่ง</option>
                            <option value="success">จัดส่งสำเร็จ</option>
                            <option value="cancelled">ยกเลิก</option>
                        </select>
                    </div>

                    <!-- รายการค้างตรวจสอบ -->
                    <!--[if BLOCK]><![endif]--><?php if($pendingDeliveries->isNotEmpty()): ?>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">⚠️ รายการค้างตรวจสอบ</h6>
                        <hr>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pendingDeliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-2">
                            <strong><?php echo e($pending->order_delivery_number); ?></strong><br>
                            <small><?php echo e($pending->order->customer->customer_name); ?><br>
                                วันที่: <?php echo e($pending->order_delivery_date->format('d/m/Y')); ?><br>
                                ยอด: <?php echo e(number_format($pending->order_delivery_grand_total, 2)); ?> บาท</small>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-success mb-2">📅 รายการวันนี้</h6>
                                    <h4 class="mb-0"><?php echo e(number_format($stats['today']['count'])); ?> รายการ</h4>
                                    <small class="text-success"><?php echo e(number_format($stats['today']['amount'], 2)); ?> บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-warning mb-2">⚠️ ค้างตรวจสอบ</h6>
                                    <h4 class="mb-0"><?php echo e(number_format($stats['pending']['count'])); ?> รายการ</h4>
                                    <small class="text-warning"><?php echo e(number_format($stats['pending']['amount'], 2)); ?> บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-primary mb-2">✅ ตรวจสอบแล้ว</h6>
                                    <h4 class="mb-0"><?php echo e(number_format($stats['success']['count'])); ?> รายการ</h4>
                                    <small class="text-primary"><?php echo e(number_format($stats['success']['amount'], 2)); ?> บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly/Yearly Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-info bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-info mb-2">📊 สถิติรายเดือน</h6>
                                    <h4 class="mb-0"><?php echo e(number_format($stats['monthly']['count'])); ?> รายการ</h4>
                                    <small class="text-info"><?php echo e(number_format($stats['monthly']['amount'], 2)); ?> บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-secondary bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-secondary mb-2">📈 สถิติรายปี</h6>
                                    <h4 class="mb-0"><?php echo e(number_format($stats['yearly']['count'])); ?> รายการ</h4>
                                    <small class="text-secondary"><?php echo e(number_format($stats['yearly']['amount'], 2)); ?> บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Input -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">🔍 สแกนบิล</h5>
                    <input type="text" wire:model.live.debounce.500ms="scanInput" 
                        class="form-control form-control-lg" 
                        placeholder="ยิง QR Code หรือกรอกเลขบิล"
                        autofocus>
                </div>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($currentDelivery || $todayApprovedDeliveries->isNotEmpty()): ?>
            <!-- Current Scanned Order and Today's Approved -->
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✅ ตรวจสอบล่าสุด</h5>
                </div>
                <div class="card-body">
                    <!--[if BLOCK]><![endif]--><?php if($currentDelivery): ?>
                    <div class="alert alert-success mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>เลขที่บิล:</strong> <?php echo e($currentDelivery->order_delivery_number); ?></p>
                                <p class="mb-1"><strong>ลูกค้า:</strong> <?php echo e($currentDelivery->order->customer->customer_name); ?></p>
                                <p class="mb-0"><strong>วันที่จัดส่ง:</strong> <?php echo e($currentDelivery->order_delivery_date->format('d/m/Y')); ?></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-warning" wire:click="cancelSuccess(<?php echo e($currentDelivery->id); ?>)">
                                    <i class="ri-restart-line"></i> ยกเลิกการตรวจสอบ
                                </button>
                                <a href="<?php echo e(route('orders.show', $currentDelivery->order->id)); ?>" class="btn btn-info" target="_blank">
                                    <i class="ri-external-link-line"></i> ดูรายละเอียด
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($todayApprovedDeliveries->isNotEmpty()): ?>
                    <h6 class="mb-3">รายการที่ตรวจสอบวันนี้:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>เวลา</th>
                                    <th>เลขที่บิล</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดเงิน</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $todayApprovedDeliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($delivery->order_delivery_status_date ? $delivery->order_delivery_status_date->format('H:i') : '-'); ?></td>
                                    <td><?php echo e($delivery->order_delivery_number); ?></td>
                                    <td><?php echo e($delivery->order->customer->customer_name); ?></td>
                                    <td><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-warning" wire:click="cancelSuccess(<?php echo e($delivery->id); ?>)">
                                            ยกเลิก
                                        </button>
                                        <a href="<?php echo e(route('orders.show', $delivery->order->id)); ?>" 
                                           class="btn btn-sm btn-info" target="_blank">
                                            ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if(!$currentDelivery && !$deliveries->count()): ?>
            <div class="text-center py-5 text-muted">
                <i class="ri-qr-scan-2-line" style="font-size: 64px;"></i>
                <p class="mt-3">กรุณายิง QR Code หรือเลือกตัวกรองเพื่อดูรายการ</p>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- Deliveries Table -->
            <!--[if BLOCK]><![endif]--><?php if($deliveries->count() > 0): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">📋 รายการจัดส่ง</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>วันที่</th>
                                    <th>เลขที่บิล</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดเงิน</th>
                                    <th>สถานะ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($delivery->order_delivery_date->format('d/m/Y')); ?></td>
                                    <td><?php echo e($delivery->order_delivery_number); ?></td>
                                    <td><?php echo e($delivery->order->customer->customer_name); ?></td>
                                    <td><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->order_delivery_status === 'success'): ?>
                                            <span class="badge bg-success">ตรวจสอบแล้ว</span>
                                        <?php elseif($delivery->order_delivery_status === 'pending'): ?>
                                            <span class="badge bg-warning">รอตรวจสอบ</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">ยกเลิก</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->order_delivery_status === 'success'): ?>
                                        <button class="btn btn-sm btn-warning" wire:click="cancelSuccess(<?php echo e($delivery->id); ?>)">
                                            ยกเลิกการตรวจสอบ
                                        </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <a href="<?php echo e(route('orders.show', $delivery->order->id)); ?>" 
                                           class="btn btn-sm btn-info" target="_blank">
                                            ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <?php echo e($deliveries->links()); ?>

                    </div>
                </div>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('9c3dd396-add1-4e4e-8728-512a129aea74')): $__env->markAsRenderedOnce('9c3dd396-add1-4e4e-8728-512a129aea74');
$__env->startPush('scripts'); ?>
<script>
    // Auto-focus scan input when page loads
    document.addEventListener('livewire:initialized', () => {
        const scanInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="scanInput"]');
        if (scanInput) {
            scanInput.focus();
        }
    });

    // Re-focus scan input after successful scan
    document.addEventListener('notify', () => {
        setTimeout(() => {
            const scanInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="scanInput"]');
            if (scanInput) {
                scanInput.focus();
            }
        }, 100);
    });
</script>
<?php $__env->stopPush(); endif; ?>


<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/scan-invoice.blade.php ENDPATH**/ ?>