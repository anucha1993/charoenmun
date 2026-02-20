<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav" >
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg" >
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    
                    

                     
                      
                   
                    
                    <li class="nav-item dropdown" >
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('customers.index')); ?>">
                            <i class="ri-briefcase-line"></i>ข้อมูลลูกค้า 
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pages-line"></i>ใบเสนอราคา <i class="mdi mdi-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?php echo e(route('quotations.index')); ?>" class="dropdown-item">
                                <i class="ri-file-list-line me-2"></i>ทั้งหมด
                            </a>
                            <a href="<?php echo e(route('quotations.pending')); ?>" class="dropdown-item">
                                <i class="ri-time-line me-2 text-warning"></i>รออนุมัติ
                            </a>
                            <a href="<?php echo e(route('quotations.approved')); ?>" class="dropdown-item">
                                <i class="ri-checkbox-circle-line me-2 text-success"></i>อนุมัติแล้ว
                            </a>
                            <a href="<?php echo e(route('quotations.cancelled')); ?>" class="dropdown-item">
                                <i class="ri-close-circle-line me-2 text-danger"></i>ไม่อนุมัติ
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('orders.index')); ?>">
                            <i class="ri-numbers-line"></i>คำสั่งซื้อ
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-bar-chart-box-line"></i>รายงาน <i class="mdi mdi-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?php echo e(route('reports.sales')); ?>" class="dropdown-item">
                                <i class="ri-user-star-line me-2"></i>สถิติการขายของ Sale
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('scan.invoice')); ?>">
                            <i class="ri-qr-code-line"></i>ScanQrcode
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('payments.confirm')); ?>">
                            <i class="ri-money-dollar-box-line"></i>อนุมัติยอด
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('deliveries.calendar')); ?>">
                            <i class="ri-calendar-event-line"></i>ปฏิทินจัดส่งสินค้า
                        </a>
                    </li>
                       
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-layouts" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-layout-line"></i>ตั้งค่าระบบ <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-layouts">
                            <a href="<?php echo e(route('products.index')); ?>" class="dropdown-item">สิ้นค้าทั้งหมด</a>
                            <a href="<?php echo e(route('global-sets.index')); ?>" class="dropdown-item">GlobalSets</a>
                            <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                                <a href="<?php echo e(route('users.index')); ?>" class="dropdown-item">จัดการผู้ใช้งาน</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== --><?php /**PATH C:\laragon\www\charoenmun\resources\views/layouts/shared/horizontal-nav.blade.php ENDPATH**/ ?>