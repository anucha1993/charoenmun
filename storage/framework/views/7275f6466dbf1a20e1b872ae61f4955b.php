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

                        <!-- ที่อยู่จัดส่ง -->
                        <div class="mb-2">
                            <label for="delivery_address" class="form-label">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control" wire:model="deliveryForm.delivery_address" 
                                rows="4" placeholder="กรอกที่อยู่จัดส่งแบบเต็ม (เลขที่ หมู่ ซอย ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์)"></textarea>
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