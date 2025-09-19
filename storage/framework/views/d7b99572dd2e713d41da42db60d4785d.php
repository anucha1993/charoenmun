

<?php $__env->startSection('title', 'จัดการผู้ใช้งาน'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="header-title">จัดการผู้ใช้งาน</h4>
                        <p class="text-muted mb-0">รายการผู้ใช้งานทั้งหมดในระบบ</p>
                    </div>
                    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                        <i class="ri-add-circle-line"></i> เพิ่มผู้ใช้งาน
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>ชื่อ</th>
                                <th>อีเมล</th>
                                <th>ประเภท</th>
                                <th>วันที่สร้าง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php if($user->type === 'admin'): ?>
                                            <span class="badge bg-primary">ผู้ดูแลระบบ</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">สมาชิก</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($user->created_at->format('d/m/Y H:i')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('users.edit', $user->id)); ?>" class="btn btn-sm btn-info">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.horizontal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\charoenmun\resources\views/users/index.blade.php ENDPATH**/ ?>