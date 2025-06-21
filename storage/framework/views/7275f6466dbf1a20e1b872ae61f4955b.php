<div>
    <!-- Modal -->
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="bs-example-modal-lg" tabindex="-1" aria-hidden="true">
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
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <input type="text" class="form-control" wire:model.defer="customer_name">
                    </div>


                    <form wire:submit.prevent="<?php echo e($editing ? 'updateDelivery' : 'saveDelivery'); ?>">

                        <div class="mb-2">
                            <label for="username" class="form-label">ชื่อผู้ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_contact_name" type="text"
                                placeholder="ชื่อผู้ติดต่อ">
                        </div>
                        <div class="mb-2">
                            <label for="username" class="form-label">เบอร์ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_phone" type="text"
                                placeholder="เบอร์ติดต่อ">
                        </div>

                        <div class="mb-2">
                            <label for="username" class="form-label">เลขที่/หมู่/ซอย</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_number" type="text"
                                placeholder="เลขที่/หมู่/ซอย">
                        </div>

                        <!-- จังหวัด -->
                        <div class="mb-2">
                            <label for="">จังหวัด</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_province" >
                                <option value="">-- เลือกจังหวัด --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveryProvinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <!-- อำเภอ -->
                        <div class="mb-2">
                            <label for="">อำเภอ/เขต</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_amphur"
                                <?php if(!$deliveryAmphures): echo 'disabled'; endif; ?> >
                                <option value="">-- เลือกอำเภอ --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveryAmphures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"
                                        <?php if($deliveryForm['delivery_amphur'] == $code): ?> selected <?php endif; ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <!-- ตำบล -->
                        <div class="mb-2">
                            <label for="">ตำบล/แขวง</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_district"
                                <?php if(!$deliveryDistricts): echo 'disabled'; endif; ?> >
                                <option value="">-- เลือกตำบล --</option>

                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveryDistricts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>"
                                        <?php if($deliveryForm['delivery_district'] == $code): ?> selected <?php endif; ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->


                            </select>
                        </div>

                        <!-- รหัสไปรษณีย์ -->
                        <div class="mb-2">
                            <label for="">รหัสไปรษณีย์ <span class="text-primary"
                                    style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                            <input type="text" wire:model.live.debounce.500ms="deliveryForm.delivery_zipcode"
                                class="form-control">
                        </div>

                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal"
                                data-bs-dismiss="modal">ปิด</button>
                            <!--[if BLOCK]><![endif]--><?php if($editing): ?>
                                <button type="submit" class="btn btn-warning">อัปเดต</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/quotations/delivery-address-modal.blade.php ENDPATH**/ ?>