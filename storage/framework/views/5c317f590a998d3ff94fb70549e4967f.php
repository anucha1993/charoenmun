<div>
    

    <div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="submitPayment" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แจ้งชำระเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <strong>บิลหลัก:</strong> <?php echo e($orderNumber); ?><br>
                        <strong>บิลย่อย:</strong> <?php echo e($orderDeliveryNumber); ?>

                    </div>
                    <hr>

                    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php if(session()->has('error')): ?>
                        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="mode" id="modeApi" wire:model.live="mode"
                            value="api">
                        <label class="btn btn-outline-primary" for="modeApi">ตรวจสลิป</label>

                        <input type="radio" class="btn-check" name="mode" id="modeManual" wire:model.live="mode"
                            value="manual">
                        <label class="btn btn-outline-secondary" for="modeManual">แมนนวลสลิป</label>
                    </div>

                    

                    <div wire:loading wire:target="slip" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">กำลังตรวจสอบสลิป...</div>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($preview): ?>
                        <div class="mb-3">
                            <label class="form-label">สลิปที่อัปโหลด:</label><br>
                            <img src="<?php echo e($preview); ?>" class="img-fluid border" style="max-height: 300px;">
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->



                    
                    
                    <!--[if BLOCK]><![endif]--><?php if($mode === 'api'): ?>
                        <div class="mb-3">
                            <label class="form-label">อัปโหลดสลิป</label>
                            <input type="file" wire:model="slip" wire:key="slip-api" class="form-control">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['slip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($slipData): ?>
                        <div class="border p-2 rounded bg-light mb-3">
                                <p><strong>ยอดเงิน:</strong> <?php echo e(number_format($slipData['amount'], 2)); ?> บาท</p>
                                <p><strong>ชื่อผู้โอน:</strong> <?php echo e($slipData['sender_name']); ?></p>
                                <p><strong>บัญชีผู้รับ:</strong> <?php echo e($slipData['receiver_name']); ?></p>
                                <p><strong>วันที่โอน:</strong>
                                    <?php echo e(\Carbon\Carbon::parse($slipData['transfer_at'])->format('d/m/Y H:i')); ?></p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <!--[if BLOCK]><![endif]--><?php if($mode === 'manual'): ?>
                        <div class="mb-3">
                            <label class="form-label">อัปโหลดสลิป</label>
                            <input type="file" wire:model="slip"  wire:key="slip-manual" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>ธนาคารผู้โอน</label>
                            
                            <select id="manualBankName" class="form-select" wire:model="manual.bank_name">
                                <option value="">-- เลือกธนาคาร --</option>
                                <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                                <option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
                                <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                                <option value="ธนาคารทหารไทยธนชาติ">ธนาคารทหารไทยธนชาติ</option>
                                <option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
                                <option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
                                <option value="ธนาคารเกียรตินาคินภัทร">ธนาคารเกียรตินาคินภัทร</option>
                                <option value="ธนาคารซีไอเอ็มบีไทย">ธนาคารซีไอเอ็มบีไทย</option>
                                <option value="ธนาคารทิสโก้">ธนาคารทิสโก้</option>
                                <option value="ธนาคารยูโอบี">ธนาคารยูโอบี</option>
                                <option value="ธนาคารไทยเครดิตเพื่อรายย่อย">ธนาคารไทยเครดิตเพื่อรายย่อย</option>
                                <option value="ธนาคารแลนด์ แอนด์ เฮ้าส์">ธนาคารแลนด์ แอนด์ เฮ้าส์</option>
                                <option value="ธนาคารไอซีบีซี (ไทย)">ธนาคารไอซีบีซี (ไทย)</option>
                                <option value="ธนาคารเพื่อการเกษตรและสหกรณ์">ธนาคารเพื่อการเกษตรและสหกรณ์</option>
                                <option value="ธนาคารออมสิน">ธนาคารออมสิน</option>
                                <option value="ธนาคารอาคารสงเคราะห์">ธนาคารอาคารสงเคราะห์</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>เลขที่บัญชีผู้โอน</label>
                            <input type="text" wire:model="manual.account_no" class="form-control" placeholder="xxxxxxxxxx">
                        </div>
                        <div class="mb-2">
                            <label>ชื่อผู้โอน</label>
                            <input type="text" wire:model="manual.sender_name" class="form-control" placeholder="ชื่อผู้โอน">
                        </div>
                        <div class="mb-2">
                            <label>วันที่โอน</label>
                            <input type="datetime-local" wire:model="manual.transfer_at" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>ชื่อผู้รับ</label>
                            <input type="text" wire:model="manual.receiver_name" class="form-control" placeholder="ชื่อผู้รับ">
                        </div>
                        <div class="mb-2">
                            <label>เลขที่บัญชีผู้รับ</label>
                            <input type="text" wire:model="manual.receiver_account_no" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>จำนวนเงิน</label>
                            <input type="number" step="0.01" wire:model="manual.amount" class="form-control">
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="slip" data-bs-dismiss="modal">
                        <span wire:loading wire:target="slip">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            รอการตรวจสอบ...
                        </span>
                        <span wire:loading.remove wire:target="slip">
                            บันทึก
                        </span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', () => {
        // เปิด Modal ด้วย JS
        Livewire.on('open-payment-modal', () => {
            const modalEl = document.getElementById('paymentModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        // ปิด Modal
        Livewire.on('close-payment-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (modal) modal.hide();
        });
    });
</script>


<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/payment-modal.blade.php ENDPATH**/ ?>