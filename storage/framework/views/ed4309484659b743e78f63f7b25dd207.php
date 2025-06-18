<div>
    <br>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">ออเดอร์ทั้งหมด</h5>
                    <p class="card-text fs-4"><?php echo e($totalOrders); ?></p>
                </div>
            </div>
        </div>
    
        
        
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $paymentSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <h6 class="card-title"><?php echo payment_status_badge($status); ?></h6>
                        <p class="card-text fs-4"><?php echo e($count); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
      
    
        
      
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <h6 class="card-title"><?php echo order_status_badge($status); ?></h6>
                        <p class="card-text fs-4"><?php echo e($count); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
       
    </div>

    <div class="card">
        <div class="card-header">
            <h4>รายการสั่งซื้อ</h4>
        </div>
        <div class="card-body">


    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>เลขที่ Order</th>
                <th>วันที่</th>
                <th>ชื่อลูกค้า</th>
                <th>ยอดรวม</th>
                <th>ชำระเงิน</th>
                <th>สถานะจัดส่ง</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($orders->firstItem() + $index); ?></td>
                    <td><?php echo e($order->order_number); ?></td>
                    <td><?php echo e($order->order_date->format('d/m/Y')); ?></td>
                    <td><?php echo e($order->customer->customer_name ?? '-'); ?></td>
                    <td><?php echo e(number_format($order->order_grand_total, 2)); ?></td>
                    <td>
                        <?php echo payment_status_badge($order->payment_status); ?>

                       
                    </td>
                    <td><?php echo order_status_badge($order->order_status); ?></td>
                    <td>
                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-sm btn-primary">ข้อมูลการขาย</a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo e($order->id); ?>)">ลบ</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">ไม่มีข้อมูล</td>
                </tr>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </tbody>
    </table>

    <div class="mt-3">
        <?php echo e($orders->links('pagination::bootstrap-5')); ?>


    </div>

</div>
</div>

    <!-- Confirm Delete -->
    <script>
        function confirmDelete(id) {
            if (confirm('คุณแน่ใจว่าต้องการลบรายการนี้?')) {
                window.livewire.emit('deleteOrder', id);
            }
        }
    </script>
</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/order-index.blade.php ENDPATH**/ ?>