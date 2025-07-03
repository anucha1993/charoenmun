<div>
    <?php
        $copies = ['ต้นฉบับ (ลูกค้า)', 'สำเนา (คลังสินค้า)', 'สำเนา (พนักงานขับรถ)','สำเนา (ฝ่ายบัญชี)'];
        $copiesTotal = count($copies);
        $totalPages = ceil($delivery->deliveryItems->count() / 8) * $copiesTotal ;
        $loopIndex = 1;
        $showPricePages = request('show_price', []);
        
    ?>
    <!-- ปุ่มพิมพ์โดยตรง -->
    <div class="d-print-none text-center mb-4">
        <button class="btn btn-danger" wire:click="showPrintConfirmation">
            <i class="ri-printer-line"></i> พิมพ์ใบส่งของ
        </button>
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> กลับ
        </a>
    </div>
    
    <!-- Modal ยืนยันการพิมพ์ -->
    <div class="modal fade <?php echo e($showPrintModal ? 'show' : ''); ?>" id="printConfirmModal" tabindex="-1" role="dialog" 
        style="<?php echo e($showPrintModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;'); ?>">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการพิมพ์ใบส่งของ</h5>
                    <button type="button" class="btn-close" wire:click="$set('showPrintModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="printedBy">ชื่อผู้พิมพ์</label>
                        <input type="text" class="form-control" id="printedBy" wire:model="printedBy">
                    </div>
                    <p>นี่เป็นการพิมพ์ครั้งที่ <?php echo e($printCount + 1); ?> ของใบส่งของฉบับนี้</p>
                    <!--[if BLOCK]><![endif]--><?php if($isCompleteDelivery): ?>
                        <div class="alert alert-success">
                            <i class="ri-checkbox-circle-line me-2"></i> ใบส่งของนี้เป็นการส่งสินค้าครบตามใบสั่งซื้อแล้ว
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo e($errorMessage); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showPrintModal', false)">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" wire:click="confirmPrint">ยืนยันการพิมพ์</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal กรอกรหัสยืนยัน -->
    <div class="modal fade <?php echo e($showAuthCodeModal ? 'show' : ''); ?>" id="authCodeModal" tabindex="-1" role="dialog" 
        style="<?php echo e($showAuthCodeModal ? 'display: block; background-color: rgba(0,0,0,0.5);' : 'display: none;'); ?>">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">กรุณากรอกรหัสยืนยัน</h5>
                    <button type="button" class="btn-close" wire:click="$set('showAuthCodeModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>คุณได้พิมพ์ใบส่งของนี้ไปแล้ว <?php echo e($printCount); ?> ครั้ง</p>
                    <p>หากต้องการพิมพ์อีกครั้ง กรุณากรอกรหัสยืนยัน</p>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" placeholder="กรอกรหัสยืนยัน" wire:model="authCode">
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo e($errorMessage); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showAuthCodeModal', false)">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" wire:click="verifyAuthCode">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>
    
<!--[if BLOCK]><![endif]--><?php $__currentLoopData = $copies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $copyIndex => $copyName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $delivery->deliveryItems->chunk(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunkIndex => $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card row text-black page-copy" >
            <div class="card-body">
                <!-- Invoice Detail-->
                <div class="clearfix">
                    <div class="float-start">
                        <img src="/images/logo-cmc.png" class="mb-0" alt="dark logo" height="60">
                        <h4 class="m-0 mb-0">Order Delivery / ใบส่งสินค้า</h4>
                    </div>

                    <div class="float-center">

                        <div class="float-end">

                            <img src="<?php echo e(route('qr.deliveries', $delivery->id)); ?>" alt="QR"
                                style="height:100px;"><br>
                            <small class="float-end">หน้า <?php echo e($copyIndex + 1); ?>/<?php echo e($totalPages); ?></small>
                        </div>

                    </div>

                </div>


                <div class="row text-black">
                    <div class="col-sm-6">
                        <div class="float-start">
                            <p><b>บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)</b></p>
                            <p class=" fs-13" style="margin-top: -10px">ที่อยู่ 99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง
                                จังหวัดนนทบุรี 11110 โทร
                                082-4789197 </br>
                                เลขประจำตัวผู้เสียภาษี 0125560015546
                            </p>
                            <!--[if BLOCK]><![endif]--><?php if($isCompleteDelivery): ?>
                                <div class="badge bg-success p-2 mt-2" style="font-size: 16px;">
                                    <i class="ri-check-double-line me-1"></i> ส่งของครบแล้ว
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="col-sm-4 offset-sm-2 mt-2">
                        <div class="mt-0 float-sm-end">
                            <span class="fs-13"><strong>วันที่เสนอราคา: </strong>
                                &nbsp;&nbsp;&nbsp;<?php echo e(date('d/m/Y', strtotime($delivery->quote_date))); ?></span> <br>
                            <span class="fs-13"><strong>เลขที่ใบส่งของ</strong>
                                &nbsp;&nbsp;&nbsp;<?php echo e($delivery->order_delivery_number); ?></span><br>
                            <span class="fs-13"><strong>เลขที่ใบสั่งซื้อ </strong>
                                &nbsp;&nbsp;&nbsp;<?php echo e($delivery->order->order_number); ?></span><br>
                            <span class="fs-13"><strong>ชื่อผู้ขาย (Sale) </strong><span class="float-end">
                                    <?php echo e($delivery->sale->name); ?></span></span><br>
                        </div>
                    </div>
                </div>

                <div class="row mt-1 ">
                    <div class="col-6">
                        <h6 class="fs-14">ข้อมูลลูกค้า</h6>
                        <address>
                            <?php echo e($delivery->order->customer->customer_name); ?><br>
                            <?php echo e($delivery->order->customer->customer_address); ?><br>
                            <?php echo e($delivery->order->customer->customer_district_name .
                                ' ' .
                                $delivery->order->customer->customer_amphur_name .
                                ' ' .
                                $delivery->order->customer->customer_province_name .
                                ' ' .
                                $delivery->order->customer->customer_zipcode); ?><br>
                            (+66) <?php echo e($delivery->order->customer->customer_phone); ?>

                        </address>
                    </div> <!-- end col-->

                    <div class="col-6">
                        <h6 class="fs-14">ที่อยู่จัดส่ง</h6>
                        <!--[if BLOCK]><![endif]--><?php if($delivery->deliveryAddress): ?>
                            <address>
                                <?php echo e($delivery->deliveryAddress->delivery_contact_name); ?>

                                (<?php echo e($delivery->deliveryAddress->delivery_phone); ?>)<br>
                                <?php echo e($delivery->deliveryAddress->delivery_number); ?><br>
                                <?php echo e($delivery->deliveryAddress->delivery_address); ?><br>

                            </address>
                        <?php else: ?>
                            <address>
                                <?php echo e($delivery->order->customer->customer_contract_name); ?> (+66)
                                <?php echo e($delivery->order->customer->customer_phone); ?> <br>
                                <?php echo e($delivery->order->customer->customer_address); ?><br>
                                <?php echo e($delivery->order->customer->customer_district_name .
                                    ' ' .
                                    $delivery->order->customer->customer_amphur_name .
                                    ' ' .
                                    $delivery->order->customer->customer_province_name .
                                    ' ' .
                                    $delivery->order->customer->customer_zipcode); ?><br>

                            </address>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    </div> <!-- end col-->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-hover  mb-0 mt-0">
                                <thead class="border-top border-bottom border-start-0 border-end-0 border-danger">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>จำนวน</th>
                                        <th>หน่วยนับ</th>
                                        <th >รายการสินค้า</th>
                                        <th class="price-section">ราคาต่อหน่วย</th>
                                        <th class="text-end price-section">จำนวนเงินรวม</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loopIndex++); ?></td>
                                            <td><?php echo e($item->quantity); ?></td>
                                            <td><?php echo e($item->orderItem->product_unit); ?></td>
                                            <td><b><?php echo e($item->orderItem->product_name); ?></b>
                                                (<?php echo e($item->orderItem->product_detail); ?>)
                                            </td>
                                            <td class="price-section"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                        <td class="text-end price-section"><?php echo e(number_format($item->total, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->


                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
                <br>

                <div class="row ">
                    <div class="col-sm-6">
                        <div class="clearfix pt-3">
                            <h6 class="text-muted fs-14">หมายเหตุ:</h6>
                            <small>
                                <?php echo e($delivery->order_deliver_note); ?>

                            </small>
                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="float-end mt-sm-0 price-section">
                            <p><b>จำนวนเงินรวม :</b> <span
                                    class="float-end"><?php echo e(number_format($delivery->order_delivery_subtotal, 2)); ?></span>
                            </p>
                            <p><b>ส่วนลด:</b> <span
                                    class="float-end"><?php echo e(number_format($delivery->order_delivery_discount, 2)); ?></span>
                            </p>
                            <p><b>ภาษีมูลค่าเพิ่ม:</b> <span
                                    class="float-end"><?php echo e(number_format($delivery->order_delivery_vat, 2)); ?></span></p>
                            <p><b>จำนวนเงินทั้งสิ้น: &nbsp; </b> <span
                                    class="float-end"><?php echo e(number_format($delivery->order_delivery_grand_total, 2)); ?></span>
                            </p>
                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>
                <!-- end row-->

                <hr>
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="clearfix">
                            <span>เงือนไขการระบสินค้า :</span><br>
                            <span>กรุณาตรวจสอบความถูกต้องของสินค้าและเซ็นรับสินค้าในวันที่ได้รับ
                                หากไม่มีการตรวจสอบหรือเซ็นรับสินค้า
                                ทางบริษัทขอสงวนสิทธิ์ในการรับผิดชอบต่อความผิดพลาดทุกกรณ</span><br>

                        </div>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-sm-6">
                        <div class="clearfix pt-4">
                            <span>ลงชื่อผู้รับสินค้า............................................................ผู้รับสินค้า</span><br>

                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="float-end mt-sm-0  pt-4">
                            <span>ลงชื่อผู้รับเงิน............................................................ผู้รับเงิน</span><br>

                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>

                
                <!-- end buttons -->

            </div> <!-- end card-body-->
        </div> <!-- end card -->

        


    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if(!$loop->last): ?>
        <div class="page-break"></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <!-- end row -->
    <style>
        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 150px;
        font-weight: bold;
        color: red;
        opacity: 0.1;
        z-index: 9999;
        pointer-events: none;
        font-style: italic;
        text-align: center;
        white-space: nowrap;
    }

    @media print {
        .watermark {
            display: none !important;
        }
    }
    </style>
<div class="watermark">ตัวอย่างก่อนพิมพ์</div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // กำหนดการแสดงราคาทันทีเมื่อโหลดหน้า
        setPagePriceVisibility();
    });

    function setPagePriceVisibility() {
        // ดึงทุกหน้า
        const allCopies = document.querySelectorAll('.page-copy');
        
        allCopies.forEach((copyEl, index) => {
            const priceEls = copyEl.querySelectorAll('.price-section');
            if (index < 3) {
                // หน้า 1-3 ไม่แสดงราคา
                priceEls.forEach(el => el.style.display = 'none');
            } else {
                // หน้า 4 แสดงราคาเสมอ
                priceEls.forEach(el => el.style.display = '');
            }
        });
    }
    
    // กลับหลังจากพิมพ์
    window.addEventListener('afterprint', () => {
        history.back(); 
    });
    
    // ฟังก์ชันสำหรับ Livewire เพื่อพิมพ์เอกสาร
    document.addEventListener('livewire:init', () => {
        Livewire.on('printDelivery', () => {
            // ซ่อนปุ่มพิมพ์ก่อนพิมพ์
            const watermark = document.querySelector('.watermark');
            if (watermark) {
                watermark.style.display = 'none';
            }
            
            // พิมพ์เอกสาร
            setTimeout(() => window.print(), 300);
        });
    });
</script>





<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/orders/order-delivery-print.blade.php ENDPATH**/ ?>