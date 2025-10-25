<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                          <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                แก้ไขที่อยู่จัดส่ง
                            <?php else: ?>
                               เพิ่มที่อยู่จัดส่ง
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal" onclick="cleanupModal('deliveryModal')"></button>
                </div>
                <div class="modal-body">
                    
                    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <?php if(session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <input type="text" class="form-control" wire:model="customer_name" readonly style="background-color: #f8f9fa;">
                    </div>


                    <form id="delivery-form" wire:submit.prevent="<?php echo e($editing ? 'updateDelivery' : 'saveDelivery'); ?>">

                        <div class="mb-2">
                            <label for="delivery_contact_name" class="form-label">ชื่อผู้ติดต่อ <span class="text-danger">*</span></label>
                            <input class="form-control <?php $__errorArgs = ['deliveryForm.delivery_contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                wire:model="deliveryForm.delivery_contact_name" type="text"
                                placeholder="ชื่อผู้ติดต่อ" required>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['deliveryForm.delivery_contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-2">
                            <label for="delivery_phone" class="form-label">เบอร์ติดต่อ</label>
                            <input class="form-control <?php $__errorArgs = ['deliveryForm.delivery_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                wire:model="deliveryForm.delivery_phone" type="text"
                                placeholder="เบอร์ติดต่อ">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['deliveryForm.delivery_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!-- ที่อยู่จัดส่ง -->
                        <div class="mb-2">
                            <label for="delivery_address" class="form-label">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control <?php $__errorArgs = ['deliveryForm.delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                wire:model="deliveryForm.delivery_address" 
                                rows="4" placeholder="กรอกที่อยู่จัดส่งแบบเต็ม (เลขที่ หมู่ ซอย ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์)"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['deliveryForm.delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </form>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal"
                        data-bs-dismiss="modal">ปิด</button>
                    <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                        <button type="submit" class="btn btn-warning" form="delivery-form">อัปเดต</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" form="delivery-form">บันทึก</button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\AP_DEV\Desktop\charoenmun\resources\views/livewire/quotations/delivery-address-modal.blade.php ENDPATH**/ ?>