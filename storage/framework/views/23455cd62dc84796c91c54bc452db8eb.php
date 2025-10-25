<div>
    <!-- ปุ่มควบคุม -->
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="ri-file-pdf-line me-2"></i>
                            ใบส่งสินค้า #<?php echo e($delivery->order_delivery_number); ?>

                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- ข้อมูลสรุป -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="ri-user-line me-2"></i>ข้อมูลลูกค้า</h6>
                                <p class="mb-1"><strong><?php echo e($delivery->order->customer->customer_name); ?></strong></p>
                                <p class="mb-1 text-muted"><?php echo e($delivery->order->customer->customer_phone); ?></p>
                                <p class="mb-0 text-muted small">
                                    <?php echo e($delivery->order->customer->customer_address); ?>

                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="ri-truck-line me-2"></i>ข้อมูลการส่ง</h6>
                                <p class="mb-1"><strong>เลขที่ใบสั่งซื้อ:</strong> <?php echo e($delivery->order->order_number); ?></p>
                                <p class="mb-1"><strong>วันที่:</strong> <?php echo e(date('d/m/Y', strtotime($delivery->quote_date))); ?></p>
                                <p class="mb-0">
                                    <strong>สถานะ:</strong> 
                                    <span class="badge <?php echo e($isCompleteDelivery ? 'bg-success' : 'bg-warning'); ?>">
                                        <?php echo e($isCompleteDelivery ? 'ส่งครบแล้ว' : 'ยังไม่ครบ'); ?>

                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- สินค้า -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="ri-package-line me-2"></i>รายการสินค้า (<?php echo e($delivery->deliveryItems->count()); ?> รายการ)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>สินค้า</th>
                                                <th>จำนวน</th>
                                                <th>หน่วย</th>
                                                <th class="text-end">ราคา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $delivery->deliveryItems->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($key + 1); ?></td>
                                                <td>
                                                    <strong><?php echo e($item->orderItem->product_name ?? ''); ?></strong>
                                                    <!--[if BLOCK]><![endif]--><?php if($item->orderItem->product_length): ?>
                                                        <br><small class="text-muted">(<?php echo e($item->orderItem->product_length); ?> <?php echo e($item->orderItem->productMeasure?->value ?? 'เมตร'); ?>)</small>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                                <td><?php echo e($item->quantity); ?></td>
                                                <td><?php echo e($item->orderItem->product_unit ?? ''); ?></td>
                                                <td class="text-end"><?php echo e(number_format($item->total, 2)); ?></td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <!--[if BLOCK]><![endif]--><?php if($delivery->deliveryItems->count() > 5): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    ... และอีก <?php echo e($delivery->deliveryItems->count() - 5); ?> รายการ
                                                </td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- สรุปยอด -->
                        <div class="row mb-4">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>ยอดรวม:</span>
                                            <span><?php echo e(number_format($delivery->order_delivery_subtotal, 2)); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>ส่วนลด:</span>
                                            <span><?php echo e(number_format($delivery->order_delivery_discount, 2)); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>ภาษี:</span>
                                            <span><?php echo e(number_format($delivery->order_delivery_vat, 2)); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <strong>ยอดสุทธิ:</strong>
                                            <strong><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่ม Actions -->
                        <div class="text-center">
                            <button class="btn btn-danger btn-lg me-2" wire:click="showPrintConfirmation">
                                <i class="ri-file-pdf-line me-2"></i>สร้าง PDF
                            </button>
                            <button class="btn btn-secondary btn-lg me-2" wire:click="downloadPDF">
                                <i class="ri-download-line me-2"></i>ดาวน์โหลด PDF
                            </button>
                            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="ri-arrow-left-line me-2"></i>กลับ
                            </a>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($printCount > 0): ?>
                        <div class="alert alert-info mt-3">
                            <i class="ri-information-line me-2"></i>
                            ใบส่งสินค้านี้ได้ถูกพิมพ์/สร้าง PDF ไปแล้ว <?php echo e($printCount); ?> ครั้ง
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal ยืนยันการสร้าง PDF -->
    <div class="modal fade <?php echo e($showPrintModal ? 'show' : ''); ?>" id="printConfirmModal" tabindex="-1" role="dialog" 
        style="<?php echo e($showPrintModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;'); ?>">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการสร้าง PDF ใบส่งของ</h5>
                    <button type="button" class="btn-close" wire:click="$set('showPrintModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="printedBy">ชื่อผู้ดำเนินการ</label>
                        <input type="text" class="form-control" id="printedBy" wire:model="printedBy">
                    </div>
                    <p>นี่เป็นการสร้าง PDF ครั้งที่ <?php echo e($printCount + 1); ?> ของใบส่งของฉบับนี้</p>
                    <!--[if BLOCK]><![endif]--><?php if($isCompleteDelivery): ?>
                        <div class="alert alert-success">
                            <i class="ri-checkbox-circle-line me-2"></i> ใบส่งของนี้เป็นการส่งสินค้าครบตามใบสั่งซื้อแล้ว
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo e($errorMessage); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showPrintModal', false)">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" wire:click="confirmPrint">สร้าง PDF</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal กรอกรหัสยืนยัน -->
    <div class="modal fade <?php echo e($showAuthCodeModal ? 'show' : ''); ?>" id="authCodeModal" tabindex="-1" role="dialog" 
        style="<?php echo e($showAuthCodeModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;'); ?>">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">กรุณากรอกรหัสยืนยัน</h5>
                    <button type="button" class="btn-close" wire:click="$set('showAuthCodeModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>คุณได้สร้าง PDF ของใบส่งของนี้ไปแล้ว <?php echo e($printCount); ?> ครั้ง</p>
                    <p>หากต้องการสร้างอีกครั้ง กรุณากรอกรหัสยืนยัน</p>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" placeholder="กรอกรหัสยืนยัน" wire:model="authCode">
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo e($errorMessage); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showAuthCodeModal', false)">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" wire:click="verifyAuthCode">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }
        
        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .modal.show {
            animation: fadeIn 0.15s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            // สำหรับ preview PDF
            Livewire.on('previewPdfInNewTab', (data) => {
                window.open(data.url, '_blank');
            });
            
            // สำหรับ download PDF (ปุ่มดาวน์โหลด)
            Livewire.on('openPdfInNewTab', (data) => {
                window.open(data.url, '_blank');
            });
        });
    </script>
</div><?php /**PATH C:\Users\AP_DEV\Desktop\charoenmun\resources\views/livewire/orders/order-delivery-print-pdf.blade.php ENDPATH**/ ?>