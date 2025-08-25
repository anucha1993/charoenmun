<div class="container py-3">


     <div class="">
            <h4 class="mb-3">🔍 สแกนบิล</h4>

            <input type="text" wire:model.live.debounce.500ms="scanInput" autofocus class="form-control mb-4"
                placeholder="ยิง QR Code หรือกรอกเลขบิล">

            <!--[if BLOCK]><![endif]--><?php if(!$order): ?>
                <div class="d-flex align-items-center justify-content-center border rounded bg-white py-5"
                    style="height: 300px;">
                    <div class="text-center text-muted">
                       <i class="ri-qr-scan-2-line" style="font-size: 100px;"></i>
                        <p class="mt-3 mb-0 text-success">กรุณายิง QR Code หรือกรอกเลขบิล</p>
                        <small class="text-secondary">ระบบจะแสดงข้อมูลใบสั่งซื้อที่เกี่ยวข้อง</small>
                    </div>
                </div>
        
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

    <!--[if BLOCK]><![endif]--><?php if($order): ?>
        <?php
            $totalConfirmed = $order->deliveries->flatMap->payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
            $totalWaiting = $order->deliveries->flatMap->payments->where('status', 'รอยืนยันยอด')->sum('amount');
        ?>



        <div class="card mb-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📄 ใบสั่งซื้อ: <?php echo e($order->order_number); ?></h5>
                <span><?php echo e($order->order_date->format('d/m/Y')); ?></span>
            </div>
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col-md-6">
                        <h6>👤 ข้อมูลลูกค้า</h6>
                        <p class="mb-1"><strong><?php echo e($order->customer->customer_name); ?></strong></p>
                        <p class="mb-1"><?php echo e($order->customer->customer_address); ?></p>
                        <p class="mb-1">
                            <?php echo e($order->customer->customer_district_name); ?>,
                            <?php echo e($order->customer->customer_amphur_name); ?>,
                            <?php echo e($order->customer->customer_province_name); ?> <?php echo e($order->customer->customer_zipcode); ?>

                        </p>
                        <p>โทร: <?php echo e($order->customer->customer_phone); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>🚚 ที่อยู่จัดส่ง</h6>
                        <!--[if BLOCK]><![endif]--><?php if($order->deliveryAddress): ?>
                            <p class="mb-1"><?php echo e($order->deliveryAddress->delivery_contact_name); ?>

                                (<?php echo e($order->deliveryAddress->delivery_phone); ?>)</p>
                            <p class="mb-1"><?php echo e($order->deliveryAddress->delivery_number); ?></p>
                            <p>
                                <?php echo e($order->deliveryAddress->delivery_address); ?>

                            </p>
                        <?php else: ?>
                            <span class="text-muted">ไม่ได้ระบุที่อยู่จัดส่ง</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <hr>

                <div class="row mb-0">
                    <div class="col-md-6">
                        <h6>💳 สถานะการเงิน</h6>
                        <p>สถานะใบสั่งซื้อ: <?php echo order_status_badge($order->order_status); ?></p>
                        <p>สถานะชำระเงิน: <?php echo payment_status_badge($order->payment_status); ?></p>
                        <p>รอยืนยันยอด: <span class="text-warning"><?php echo e(number_format($totalWaiting, 2)); ?> บาท</span></p>
                        <p>ชำระแล้ว: <span class="text-success"><?php echo e(number_format($totalConfirmed, 2)); ?> บาท</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>💰 สรุปยอด</h6>
                        <p>ส่วนลด: <?php echo e(number_format($order->order_discount, 2)); ?> บาท</p>
                        <p>VAT: <?php echo e(number_format($order->order_vat, 2)); ?> บาท</p>
                        <p><strong>รวมทั้งหมด: <?php echo e(number_format($order->order_grand_total, 2)); ?> บาท</strong></p>
                    </div>
                </div>

                <h6 class="mb-2">📦 รายการสินค้า</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>สินค้า</th>
                                <th>รายละเอียด</th>
                                <th>จำนวนสั่ง</th>
                                <th>หน่วย</th>
                                <th>ราคา/หน่วย</th>
                                <th class="text-end">ยอดรวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $delivered = $deliveredQtyMap[$item->product_id] ?? 0;
                                ?>
                                <tr>
                                    <td><?php echo e($idx + 1); ?></td>
                                    <td><?php echo e($item->product_name); ?></td>
                                    <td><?php echo e($item->product_detail); ?></td>
                                    <td><?php echo e($item->quantity); ?> <?php echo e($delivered > 0 ? '(' . $delivered . ')' : ''); ?></td>
                                    <td><?php echo e($item->product_unit); ?></td>
                                    <td><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($item->total, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4">🚚 รายการจัดส่ง</h6>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เลขบิลย่อย</th>
                            <th>ยอดรวม</th>
                            <th>สถานะจัดส่ง</th>
                            <th>สถานะชำระเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $order->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($delivery->order_delivery_date->format('d/m/Y')); ?></td>
                                <td><?php echo e($delivery->order_delivery_number); ?></td>
                                <td><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></td>
                                <td><?php echo order_delivery_status_badge($delivery->order_delivery_status); ?></td>
                                <td><?php echo payment_status_badge($delivery->payment_status); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
   

    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->



</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/scan-invoice.blade.php ENDPATH**/ ?>