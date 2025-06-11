<div>
    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-secondary border">
                <div class="card-body">
                    <h3 class="card-title">จำนวนลูกค้าทั้งหมด</h3>
                    <h2 class="my-2"><?php echo e($customers->count()); ?> ราย</h2>
                    <p class="card-text" style="font-size: 18px">นับจำนวนจากข้อมูลลูกค้าทั้งหมด</p>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-md-4">
            <div class="card border-primary border">
                <div class="card-body">
                    <h3 class="card-title text-primary">ลูกค้ารายใหม่</h3>
                    <h2 class="my-2"><?php echo e($customers->count()); ?> ราย</h2>
                    <p class="card-text" style="font-size: 18px">คิดจากลูกค้าที่เพิ่มในระบบในระยะเวลา 3 เดือน</p>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-md-4">
            <div class="card border-success border">
                <div class="card-body">
                    <h3 class="card-title text-success">ลูกค้าที่ไม่การเคลือนไหว</h3>
                    <h2 class="my-2"><?php echo e($customers->count()); ?> ราย</h2>
                    <p class="card-text" style="font-size: 18px">คิดจากลูกค้าที่ไม่มีการสั่งซื้อสินค้าเป็นระยะยเวลานาน
                    </p>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row -->

    <div class="card" style="font-size: 18px">
        <div class="card-header">
            <h3>รายการข้อมูลลูกค้าทั้งหมด</h3>
            <a href="<?php echo e(route('customers.create')); ?>" class="float-end btn btn-outline-success"
                style="font-size: 18px">+ เพิ่มข้อมูลลูกค้า</a>
        </div>
        <div class="card-body">

            
            <div class="mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    style="font-size: 18px" placeholder="ค้นหา ชื่อ, เบอร์โทร, อีเมล...">
            </div>

            

            <div class="table-responsive">
                <table class="table table">
                    <thead class="table-dark">
                        <tr>
                            <th>รหัสลูกค้า</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ชื่อผู้ติดต่อ</th>
                            <th>เบอร์โทร</th>
                            <th>ประเภท</th>
                            <th>ระดับ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($customer->customer_code); ?></td>
                                <td><?php echo e($customer->customer_name); ?></td>
                                <td><?php echo e($customer->customer_contract_name); ?></td>
                                <td><?php echo e($customer->customer_phone); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo e($customer->type->value); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo e($customer->level->value); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('customers.edit', $customer->id)); ?>" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-grease-pencil"></i> แก้ไข</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo e($customer->id); ?>)">
                                        <i class="mdi mdi-trash-can"></i> ลบ
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">ไม่พบข้อมูล</td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div>
        <?php echo e($customers->links('pagination::bootstrap-5')); ?>


    </div>
    

    <script>
        function confirmDelete(id) {
            if (confirm('คุณต้องการลบลูกค้ารายนี้หรือไม่?')) {
                Livewire.dispatch('deleteCustomer', {
                    id
                }); // object shorthand
            }
        }
    </script>
</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/customers/customer-index.blade.php ENDPATH**/ ?>