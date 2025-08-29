

 <div>

    <br>

    <div class="row">
        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-pink">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-eye-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">ยอดคำสั่งซื้อ</h6>
                    <h2 class="my-2"><?php echo e(number_format($totalOrderAmount, 2)); ?></h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">จำนวน <?php echo e($totalOrders); ?> รายการ</span>
                        <span class="text-nowrap">แสดงราคาทั้งหมดของคำสั่งซื้อ</span>
                    </p>

                    
                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-purple">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-wallet-2-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">จำนวนใบสั่งซื้อ</h6>
                    <h2 class="my-2"><?php echo e($totalOrders); ?> รายการ</h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">รายการ</span>
                        <span class="text-nowrap">แสดงจำนวนใบสั่งซื้อทั้งหมด</span>
                    </p>

                    
                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-info">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-shopping-basket-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">สินค้ารอส่ง</h6>
                    <h2 class="my-2"><?php echo e($pendingDeliveries); ?> รายการ</h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-25 me-1">จำนวนใบสั่งซื้อรอจัดส่ง</span>
                        <span class="text-nowrap">คิดจากคำสั่งซื้อทั้งหมด</span>
                    </p>
                </div>
            </div>
        </div> <!-- end col-->


          <div class="col-xxl-3 col-sm-6">
            <div class="card widget-flat text-bg-primary">
                <div class="card-body">
                    <div class="float-end">
                        <i class="ri-group-2-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">Pocket Money</h6>
                    <h2 class="my-2">
                        <strong><?php echo e(number_format($customer_pocket_money, 2)); ?> บาท</strong>
                    </h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1">จำนเงินคงเหลือ</span>
                        <a href="#" class="text-warning" wire:click="$dispatch('openPocketMoneyModal')"
                            class="">
                            แก้ไขวงเงิน
                        </a>
                    </p>

                </div>
            </div>
        </div> <!-- end col-->

    </div>




    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ข้อมูลลูกค้า</h4>
                    <p class="text-muted mb-0">
                        รายละเอียดข้อมูลลูกค้า <code>Customer</code>.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="" wire:submit.prevent="update">
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
                                            <option value="<?php echo e($option['id']); ?>"><?php echo e($option['value']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">ระดับลุกค้า <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="customer_level" required>
                                        <option value="">--กรุณาเลือก--</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerLevel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($option['id']); ?>"><?php echo e($option['value']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">ชื่อลูกค้า <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="customer_name" id="" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">เลขประจำตัวผู้เสียภาษี</label>
                                    <input type="text" wire:model="customer_taxid" id=""
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="">ชื่อผู้ติดต่อ</label>
                                    <input type="text" wire:model="customer_contract_name" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">เบอร์โทร</label>
                                    <input type="text" wire:model="customer_phone" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Email</label>
                                    <input type="text" wire:model="customer_email" id=""
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="">Line ID</label>
                                    <input type="text" wire:model="customer_idline" id=""
                                        class="form-control">
                                </div>

                            </div>
                            <div class="row">
                                <h5>ที่อยู่ลูกค้า</h5>
                                <hr>

                                <div class="col-md-12 mb-2">
                                    <label for="">ที่อยู่</label>
                                    <textarea wire:model="customer_address" class="form-control" rows="3"
                                        placeholder="กรอกที่อยู่ลูกค้า เช่น 99/35 หมู่ 9 ซอยสุขใจ"></textarea>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">
                                บันทึก
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>







        <div class="col-xl-6">


            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ประวัติการจ่ายเงิน Pocket Money</h4>
                    <p class="text-muted mb-0">
                        ประวัติการจ่ายเงิน <code> Pocket Money </code>.
                    </p>

                </div>
                <div class="card-body">
                    <div class="responsive">
                        <!--[if BLOCK]><![endif]--><?php if($paymentHistory && count($paymentHistory) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>เลขที่คำสั่งซื้อ</th>
                                                <th>ประเภท</th>
                                                <th>จำนวนเงิน</th>
                                                <th>สถานะ</th>
                                                <th>สลิป</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $paymentHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e(\Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y H:i')); ?></td>
                                                        <td>
                                                            <a href="<?php echo e(route('orders.show', $payment['order']['id'])); ?>" class="text-body fw-bold">
                                                                <?php echo e($payment['order']['order_number']); ?>

                                                            </a>
                                                        </td>
                                                        <td>
                                                            <!--[if BLOCK]><![endif]--><?php switch($type):
                                                                case ('cash'): ?>
                                                                    <span class="badge bg-success">เงินสด</span>
                                                                    <?php break; ?>
                                                                <?php case ('transfer'): ?>
                                                                    <span class="badge bg-info">โอนเงิน</span>
                                                                    <?php break; ?>
                                                                <?php case ('pocket_money'): ?>
                                                                    <span class="badge bg-warning">Pocket Money</span>
                                                                    <?php break; ?>
                                                            <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                        <td><?php echo e(number_format($payment['amount'], 2)); ?></td>
                                                        <td>
                                                            <!--[if BLOCK]><![endif]--><?php switch($payment['status']):
                                                                case ('ชำระเงินแล้ว'): ?>
                                                                    <span class="badge bg-success">ชำระแล้ว</span>
                                                                    <?php break; ?>
                                                                <?php case ('รอยืนยันยอด'): ?>
                                                                    <span class="badge bg-warning">รอยืนยัน</span>
                                                                    <?php break; ?>
                                                                <?php default: ?>
                                                                    <span class="badge bg-secondary"><?php echo e($payment['status']); ?></span>
                                                            <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                        <td>
                                                            <!--[if BLOCK]><![endif]--><?php if(isset($payment['slip_path']) && $payment['slip_path']): ?>
                                                                <a href="javascript:void(0);" onclick="showSlipModal('<?php echo e(Storage::url($payment['slip_path'])); ?>')" class="btn btn-sm btn-info">
                                                                    <i class="ri-image-line"></i> ดูสลิป
                                                                </a>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center mb-0">ไม่พบประวัติการชำระเงิน</p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>



            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">ที่อยู่จัดส่งสินค้า</h4>
                    <p class="text-muted mb-0">
                        ที่อยู่จัดส่งสินค้า <code>Customer</code>.
                    </p>

                </div>

                <div class="card-body">
                    <button type="button" wire:click="openDeliveryModal" class="btn btn-success btn-sm mb-2">
                        + เพิ่มที่อยู่
                    </button>
                    <div class="row">

                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deliveryAddresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <ul class="list-group" style="font-size: 16px">
                                <li class="list-group-item">
                                    <b>ชื่อลูกค้า : <?php echo e($address['delivery_contact_name'] ?? '-'); ?>

                                        (<?php echo e($address['delivery_phone'] ?? '-'); ?>)
                                    </b> </br>
                                    เลขที่ <?php echo e($address['delivery_number'] ?? '-'); ?> </br>
                                    <?php echo e($address['delivery_address'] ?? '-'); ?>

                                    </br>
                                    <button type="button" class="btn btn-sm btn-info"
                                        wire:click="openDeliveryModal(<?php echo e($index); ?>)">แก้ไข</button>
                                    <button type="button"
                                        onclick="if (confirm('คุณต้องการลบรายการนี้หรือไม่?') === false) event.stopImmediatePropagation();"
                                        wire:click="removeDelivery(<?php echo e($index); ?>)"
                                        class="btn btn-sm btn-danger">
                                        ลบ
                                    </button>
                                </li>

                            </ul>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                    </div>
                </div>
            </div>
        </div>



    </div>

    <!-- Modal: แก้ไข Pocket Money -->
    <div wire:ignore.self class="modal fade" id="pocketMoneyModal" tabindex="-1000" role="dialog"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form wire:submit.prevent="savePocketMoney">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขวงเงิน (Pocket Money)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pocket_money" class="form-label">วงเงินใหม่</label>
                            <input type="number" class="form-control" wire:model="customer_pocket_money">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">รหัสยืนยันการแก้ไข</label>
                            <input type="password" class="form-control" wire:model="confirm_password"
                                placeholder="***********">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </div>
            </form>
        </div>
    </div>






    <div id="signup-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>เพิ่ม/แก้ไข ข้อมูลจัดส่งสินค้า</h5>
                </div>
                <div class="modal-body">


                    <form wire:submit.prevent="saveDelivery">

                        <div class="mb-2">
                            <label for="username" class="form-label">ชื่อผู้ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_contact_name"
                                type="text" placeholder="ชื่อผู้ติดต่อ">
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

                        <!-- ที่อยู่จัดส่ง -->
                        <div class="mb-2">
                            <label for="delivery_address" class="form-label">ที่อยู่จัดส่ง</label>
                            <textarea class="form-control" wire:model="deliveryForm.delivery_address" 
                                rows="3" placeholder="กรอกที่อยู่จัดส่งแบบเต็ม (จังหวัด อำเภอ ตำบล รหัสไปรษณีย์)"></textarea>
                        </div>



                        <div class="mb-3 text-center">
                            <button  class="btn btn-primary" type="submit">บันทึก</button>
                        </div>

                    </form>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script>
        function confirmDelete(id) {
            if (confirm('ยืนยันลบที่อยู่นี้ใช่หรือไม่?')) {
                Livewire.dispatch('removeDelivery', {
                    id: id
                });
            }
        }
    </script>


    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openPocketMoneyModal', () => {
                new bootstrap.Modal(document.getElementById('pocketMoneyModal')).show();
            });
            Livewire.on('closePocketMoneyModal', () => {
                bootstrap.Modal.getInstance(document.getElementById('pocketMoneyModal'))?.hide();
            });
        });
    </script>


    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openModal', () => {
                const modalEl = document.getElementById('signup-modal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });

            Livewire.on('closeModal', () => {
                const modalEl = document.getElementById('signup-modal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            });
        });
    </script>

    

    <!-- Modal แสดงรูปสลิป -->
    <div class="modal fade" id="slipModal" tabindex="-1" role="dialog" aria-labelledby="slipModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="slipModalLabel">สลิปการโอนเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="slipImage" src="" class="img-fluid" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSlipModal(imageUrl) {
            document.getElementById('slipImage').src = imageUrl;
            new bootstrap.Modal(document.getElementById('slipModal')).show();
        }
    </script>

<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/customers/customer-edit.blade.php ENDPATH**/ ?>