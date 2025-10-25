<div wire:ignore.self class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="fullWidthModalLabel">ข้อมูลลูกค้า</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="update" id="CustomerUpdate">
                    <div class="row">
                        <div class="col-md-6  mb-2">
                            <label for="">รหัสลูกค้า </label>
                            <input type="text" wire:model="customer_code" class="form-control"
                                style="background-color: aliceblue" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="">ประเภทลูกค้า</label>
                            <select class="form-select" wire:model="customer_type">
                                <option value="">--กรุณาเลือก--</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">ระดับลูกค้า</label>
                            <select class="form-select" wire:model="customer_level">
                                <option value="">--กรุณาเลือก--</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerLevel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">ชื่อลูกค้า <span class="text-danger">*</span></label>
                            <!--[if BLOCK]><![endif]--><?php if($isDuplicateCustomer): ?>
                                <div class="col-12 mb-2">
                                    <span class="text-danger"><?php echo e($duplicateMessage); ?></span>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <input type="text" wire:model.live="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">เลขประจำตัวผู้เสียภาษี</label>
                            <!--[if BLOCK]><![endif]--><?php if($isDuplicateCustomer): ?>
                                <div class="col-12 mb-2">
                                    <span class="text-danger"><?php echo e($duplicateMessage); ?></span>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <input type="text" wire:model.live="customer_taxid" id="" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">ชื่อผู้ติดต่อ</label>
                            <input type="text" wire:model="customer_contract_name" id=""
                                class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">เบอร์โทร</label>
                            <input type="text" wire:model="customer_phone" id="" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Email</label>
                            <input type="text" wire:model="customer_email" id="" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">Line ID</label>
                            <input type="text" wire:model="customer_idline" id="" class="form-control">
                        </div>

                    </div>
                    <div class="row">
                        <h5>ที่อยู่ลูกค้า</h5>
                        <hr>

                        <div class="col-md-12 mb-2">
                            <label for="">ที่อยู่</label>
                            <textarea wire:model="customer_address" class="form-control" rows="3" placeholder="กรอกที่อยู่ลูกค้า เช่น 99/35 หมู่ 9 ซอยสุขใจ"></textarea>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    ปิด
                </button>
                <button type="submit" class="btn btn-primary" <?php echo e($isDuplicateCustomer ? 'disabled' : ''); ?> form="CustomerUpdate">
                    บันทึกข้อมูลลูกค้า
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php /**PATH C:\Users\AP_DEV\Desktop\charoenmun\resources\views/livewire/quotations/customer-modal.blade.php ENDPATH**/ ?>