<div>
    
    <script>
        /* toastr */
        window.addEventListener('notify', e => {
            const { type='success', text='' } = e.detail
            toastr.options = { timeOut: 3500, progressBar: true, positionClass: 'toast-top-right' }
            toastr[type](text)
        })

        /* bootstrap modal */
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('global-set-modal'))
            window.addEventListener('open-modal',  () => modal.show())
            window.addEventListener('close-modal', () => modal.hide())
        })
    </script>
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3 page-title-box ">
        <h3 class="page-title">Global Sets</h3>
        <button class="btn btn-primary" wire:click="create">
            <i class="ri-add-line me-1"></i> เพิ่ม
        </button>
    </div>
   

    
    <div class="card shadow-sm  ">
        <div class="card-header"><h3 class="mb-0 ">รายการ Global Sets</h3></div>

        <div class="card-body p-0">
            <table class="table table-centered mb-0" id="inline-editable" style="font-size: 18px">
                <thead>
                    <tr>
                        <th style="width:70px;">IDs</th>
                        <th>ชื่อ</th>
                        <th>คำอธิบาย</th>
                        <th class="text-center">จำนวนค่า</th>
                        <th class="text-center" style="width:140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $globalSets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $set): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($set->id); ?></td>
                            <td><?php echo e($set->name); ?></td>
                            <td><?php echo e($set->description); ?></td>
                            <td class="text-center"><?php echo e($set->values_count); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1" wire:click="edit(<?php echo e($set->id); ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        onclick="confirm('ยืนยันลบ?') || event.stopImmediatePropagation()"
                                        wire:click="delete(<?php echo e($set->id); ?>)">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">— ไม่มีข้อมูล —</td></tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <?php echo e($globalSets->links()); ?>   
        </div>
    </div>

    
    <div wire:ignore.self class="modal fade" id="global-set-modal" tabindex="-1" aria-labelledby="globalSetLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="save">   
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalSetLabel">
                            <?php echo e($editingId ? 'แก้ไข Global Set' : 'เพิ่ม Global Set'); ?>

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ *</label>
                                <input wire:model.defer="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">คำอธิบาย</label>
                                <input wire:model.defer="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Global Set Values</label>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row g-2 align-items-center mb-2">
                                        <div class="col">
                                            <input wire:model="values.<?php echo e($idx); ?>.value" class="form-control" placeholder="ค่า">
                                        </div>
                                        <div class="col-md-3">
                                            <select wire:model="values.<?php echo e($idx); ?>.status" class="form-control">
                                                <option value="Enable">Enable</option>
                                                <option value="Disable">Disable</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" wire:click="removeValue(<?php echo e($idx); ?>)" class="btn btn-sm btn-danger">ลบ</button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <button type="button" wire:click="addValue" class="btn btn-sm btn-secondary mt-2">+ เพิ่มค่า</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                        <button class="btn btn-success" type="submit">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/globalsets/global-set-manager.blade.php ENDPATH**/ ?>