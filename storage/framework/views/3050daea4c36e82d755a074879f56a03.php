<div wire:ignore.self class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="fullWidthModalLabel">Modal Heading</h4>
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
                            <label for="">ประเภทลูกค้า <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="customer_type" required>
                                <option value="">--กรุณาเลือก--</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="">ระดับลุกค้า <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="customer_level" required>
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
                            <label for="">เลขที่/หมู่/ซอย </label>
                            <input type="text" wire:model="customer_address" id="" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">จังหวัด</label>
                            <select class="form-select" wire:model.live="customer_province" required>
                                <option value="">-- เลือกจังหวัด --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"><?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">อำเภอ/เขต</label>
                            <select class="form-select" wire:model.live="customer_amphur" <?php if(!$amphures): echo 'disabled'; endif; ?>
                                required>
                                <option value="">-- เลือกอำเภอ --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $amphures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"
                                        <?php if($customer_amphur == $code): ?> selected <?php endif; ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">ตำบล/แขวง</label>
                            <select class="form-select" wire:model.live="customer_district"
                                <?php if(!$districts): echo 'disabled'; endif; ?> required>
                                <option value="">-- เลือกตำบล --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"
                                        <?php if($customer_district == $code): ?> selected <?php endif; ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="">รหัสไปรษณีย์ <span class="text-primary"
                                    style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                            <input type="text" wire:model.live.debounce.500ms="customer_zipcode" id=""
                                class="form-control">
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-primary" <?php echo e($isDuplicateCustomer ? 'disabled' : ''); ?> form="CustomerUpdate">บันทึกข้อมูล</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/charoenmun-new/resources/views/livewire/quotations/customer-modal.blade.php ENDPATH**/ ?>