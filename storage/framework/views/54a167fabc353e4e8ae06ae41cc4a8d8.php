<div class=" py-3">
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
                            <option value="pending">กำลังดำเนินการ</option>
                            <option value="processing">กำลังจัดส่ง</option>
                            <option value="success">จัดส่งสำเร็จ</option>
                            <option value="cancelled">ยกเลิกแล้ว</option>
                            <option value="returned">ส่งคืนสินค้า</option>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <strong>เลขที่บิล:</strong> <?php echo e($currentDelivery->order_delivery_number); ?>

                                        <small class="text-muted">(Order: <?php echo e($currentDelivery->order->order_number); ?>)</small>
                                    </div>
                                    <div class="mb-2">
                                        <strong>ลูกค้า:</strong> <?php echo e($currentDelivery->order->customer->customer_name); ?>

                                        <div class="small text-muted"><?php echo e($currentDelivery->order->customer->customer_address); ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <strong>วันที่จัดส่ง:</strong> <?php echo e($currentDelivery->order_delivery_date->format('d/m/Y')); ?>

                                        <div class="small text-muted">
                                            ตรวจสอบเมื่อ: <?php echo e($currentDelivery->order_delivery_status_date ? $currentDelivery->order_delivery_status_date->format('d/m/Y H:i') : '-'); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column">
                                    <!--[if BLOCK]><![endif]--><?php if($currentDelivery->selected_truck_type): ?>
                                    <div class="mb-2">
                                        <strong><i class="ri-truck-line me-1"></i>ประเภทรถ:</strong>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="me-2" style="font-size: 1.2em;">
                                                <?php echo e(truck_type_icon($currentDelivery->selected_truck_type)); ?>

                                            </span>
                                            <div>
                                                <?php echo truck_type_badge($currentDelivery->selected_truck_type); ?>

                                                <!--[if BLOCK]><![endif]--><?php if($currentDelivery->total_weight_kg > 0): ?>
                                                    <?php echo weight_status_badge($currentDelivery->total_weight_kg, $currentDelivery->selected_truck_type); ?>

                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <!--[if BLOCK]><![endif]--><?php if($currentDelivery->total_weight_kg > 0): ?>
                                    <div class="mb-2">
                                        <strong><i class="ri-weight-line me-1"></i>น้ำหนักรวม:</strong>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="fw-bold"><?php echo weight_display($currentDelivery->total_weight_kg); ?></span>
                                            <!--[if BLOCK]><![endif]--><?php if($currentDelivery->isOverweight()): ?>
                                                <small class="text-danger ms-2">
                                                    <i class="ri-alert-line"></i> เกินขีดจำกัด
                                                </small>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <div class="mb-2">
                                        <strong>จำนวนเงิน:</strong> <?php echo e(number_format($currentDelivery->order_delivery_grand_total, 2)); ?> บาท
                                    </div>
                                    
                                    <div class="btn-group">
                                        <button class="btn btn-warning" wire:click="cancelSuccess(<?php echo e($currentDelivery->id); ?>)">
                                            <i class="ri-restart-line"></i> ยกเลิกการตรวจสอบ
                                        </button>
                                        <a href="<?php echo e(route('deliveries.printer', $currentDelivery->id)); ?>" class="btn btn-info">
                                            <i class="ri-printer-line"></i> พิมพ์
                                        </a>
                                        <a href="<?php echo e(route('orders.show', $currentDelivery->order->id)); ?>" class="btn btn-secondary" target="_blank">
                                            <i class="ri-external-link-line"></i> ดูรายละเอียด
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    

                    <!--[if BLOCK]><![endif]--><?php if($todayApprovedDeliveries->isNotEmpty()): ?>
                    <h6 class="mb-3">รายการที่ตรวจสอบวันนี้:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0" 
                               style="font-size:14px; background:white; border-radius:8px; overflow:hidden;">
                            <thead>
                                <tr style="background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);">
                                    <th>เวลา</th>
                                    <th>เลขที่บิล</th>
                                    <th>ลูกค้า</th>
                                    <th>ประเภทรถ</th>
                                    <th>น้ำหนักรวม</th>
                                    <th>จำนวนเงิน</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $todayApprovedDeliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($delivery->order_delivery_status_date ? $delivery->order_delivery_status_date->format('H:i') : '-'); ?></td>
                                    <td>
                                        <div><?php echo e($delivery->order_delivery_number); ?></div>
                                        <small class="text-muted">Order: <?php echo e($delivery->order->order_number); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo e($delivery->order->customer->customer_name); ?></div>
                                        <small class="text-muted"><?php echo e(Str::limit($delivery->order->customer->customer_address, 30)); ?></small>
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->selected_truck_type): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2" style="font-size: 1.2em;">
                                                    <?php echo e(truck_type_icon($delivery->selected_truck_type)); ?>

                                                </span>
                                                <div>
                                                    <?php echo truck_type_badge($delivery->selected_truck_type); ?>

                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->total_weight_kg > 0): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold"><?php echo weight_display($delivery->total_weight_kg); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-danger" wire:click="cancelSuccess(<?php echo e($delivery->id); ?>)" title="ยกเลิกการตรวจสอบ">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                            <a href="<?php echo e(route('deliveries.printer', $delivery->id)); ?>" class="btn btn-info" title="พิมพ์">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            <a href="<?php echo e(route('orders.show', $delivery->order->id)); ?>" class="btn btn-secondary" target="_blank" title="ดูรายละเอียด">
                                                <i class="ri-external-link-line"></i>
                                            </a>
                                        </div>
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

          

            <!-- Deliveries Table -->
            
            <div class="alert alert-info mb-3">
                <strong>Debug:</strong><br>
                Filter Type: <?php echo e($filterType); ?><br>
                Status: <?php echo e($deliveryStatus); ?><br>
                <!--[if BLOCK]><![endif]--><?php if($filterType === 'date-range'): ?>
                Date Range: <?php echo e($startDate); ?> - <?php echo e($endDate); ?><br>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                Total Results: <?php echo e($deliveries->total()); ?>

            </div>

            <!--[if BLOCK]><![endif]--><?php if($deliveries->count() > 0): ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">📋 รายการจัดส่ง</h5>
                        <div>
                            <!--[if BLOCK]><![endif]--><?php if($filterType === 'today'): ?>
                                <span class="badge bg-info">รายการวันนี้</span>
                            <?php elseif($filterType === 'pending'): ?>
                                <span class="badge bg-warning">รายการค้างตรวจสอบ</span>
                            <?php elseif($filterType === 'date-range'): ?>
                                <span class="badge bg-info"><?php echo e(Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> - <?php echo e(Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            
                            <!--[if BLOCK]><![endif]--><?php if($deliveryStatus): ?>
                                <span class="badge bg-secondary">
                                    <!--[if BLOCK]><![endif]--><?php if($deliveryStatus === 'success'): ?>
                                        ตรวจสอบแล้ว
                                    <?php elseif($deliveryStatus === 'pending'): ?>
                                        รอตรวจสอบ
                                    <?php else: ?>
                                        ยกเลิก
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <small class="text-muted ms-2">(<?php echo e($deliveries->total()); ?> รายการ)</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" 
                               style="font-size:14px; background:white; border-radius:8px; overflow:hidden;">
                            <thead>
                                <tr class="table-light">
                                    <th class="align-middle" style="width: 100px;">วันที่จัดส่ง</th>
                                    <th class="align-middle" style="width: 140px;">เลขที่บิล</th>
                                    <th class="align-middle" style="width: 200px;">ลูกค้า</th>
                                    <th class="align-middle text-center" style="width: 100px;"><i class="ri-truck-line me-1"></i>ประเภทรถ</th>
                                    <th class="align-middle text-end" style="width: 100px;">น้ำหนัก</th>
                                    <th class="align-middle text-end" style="width: 120px;">จำนวนเงิน</th>
                                    <th class="align-middle text-center">สถานะ</th>
                                    <th class="align-middle text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="align-middle">
                                    <td class="align-middle">
                                        <div><?php echo e($delivery->order_delivery_date->format('d/m/Y')); ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold"><?php echo e($delivery->order_delivery_number); ?></div>
                                        <small class="text-muted">Order: <?php echo e($delivery->order->order_number); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-medium"><?php echo e($delivery->order->customer->customer_name); ?></div>
                                        <small class="text-muted"><?php echo e(Str::limit($delivery->order->customer->customer_address, 30)); ?></small>
                                    </td>
                                    <td class="align-middle text-center">
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->selected_truck_type): ?>
                                            <div class="d-inline-flex flex-column align-items-center gap-1">
                                                <span style="font-size: 1.2em;">
                                                    <?php echo e(truck_type_icon($delivery->selected_truck_type)); ?>

                                                </span>
                                                <div style="font-size: 12px;">
                                                    <?php echo truck_type_badge($delivery->selected_truck_type); ?>

                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">ไม่ได้เลือก</small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="align-middle text-end">
                                        <!--[if BLOCK]><![endif]--><?php if($delivery->total_weight_kg > 0): ?>
                                            <div class="fw-medium">
                                                <?php echo weight_display($delivery->total_weight_kg); ?>

                                                <!--[if BLOCK]><![endif]--><?php if($delivery->isOverweight()): ?>
                                                    <i class="ri-error-warning-fill text-danger" title="เกินขีดจำกัด"></i>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">ไม่ระบุ</small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="align-middle text-end fw-bold">
                                        <?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?>

                                    </td>
                                    <td class="align-middle text-center">
                                        <?php echo order_delivery_status_badge($delivery->order_delivery_status); ?>

                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">

                                            <!--[if BLOCK]><![endif]--><?php if($delivery->order_delivery_status === 'success'): ?>
                                                <button class="btn btn-outline-danger" wire:click="cancelSuccess(<?php echo e($delivery->id); ?>)" title="ยกเลิกการตรวจสอบ">
                                                    <i class="ri-close-circle-line"></i>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <a href="<?php echo e(route('deliveries.printer', $delivery->id)); ?>" class="btn btn-outline-info" title="พิมพ์">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <a href="<?php echo e(route('orders.show', $delivery->order->id)); ?>" class="btn btn-outline-secondary" target="_blank" title="ดูรายละเอียด">
                                                <i class="ri-external-link-line"></i>
                                            </a>
                                        </div>
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

<?php if (! $__env->hasRenderedOnce('dd762a24-70ec-4520-a45b-2291c439616f')): $__env->markAsRenderedOnce('dd762a24-70ec-4520-a45b-2291c439616f');
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