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
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('quotations.index')); ?>">
                            <i class="ri-pages-line"></i>ใบเสนอราคา 
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="<?php echo e(route('orders.index')); ?>">
                            <i class="ri-numbers-line"></i>คำสั่งซื้อ
                        </a>
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