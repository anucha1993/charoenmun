<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav" >
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg" >
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('any', 'index')}}" id="topnav-dashboards" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-dashboard-3-line"></i>Dashboards
                        </a>
                    </li> --}}
                    {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-apps" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pages-line"></i>Pages <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-apps">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-auth"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Authenitication <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-auth">
                                    <a href="{{ route('second', ['auth', 'login']) }}" class="dropdown-item">Login</a>
                                    <a href="{{ route('second', ['auth', 'register']) }}" class="dropdown-item">Register</a>
                                    <a href="{{ route('second', ['auth', 'logout']) }}" class="dropdown-item">Logout</a>
                                    <a href="{{ route('second', ['auth', 'forgotpw']) }}" class="dropdown-item">Forgot Password</a>
                                    <a href="{{ route('second', ['auth', 'lock-screen']) }}" class="dropdown-item">Lock Screen</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-error"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Error <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-error">
                                    <a href="{{ route('second', ['error', '404']) }}" class="dropdown-item">Error 404</a>
                                    <a href="{{ route('second', ['error', '404-alt']) }}" class="dropdown-item">Error 404-alt</a>
                                    <a href="{{ route('second', ['error', '500']) }}" class="dropdown-item">Error 500</a>
                                </div>
                            </div>
                            <a href="{{ route('second', ['pages', 'starter']) }}" class="dropdown-item">Starter Page</a>
                            <a href="{{ route('second', ['pages', 'contact-list']) }}" class="dropdown-item">Contact List</a>
                            <a href="{{ route('second', ['pages', 'profile']) }}" class="dropdown-item">Profile</a>
                            <a href="{{ route('second', ['pages', 'timeline']) }}" class="dropdown-item">Timeline</a>
                            <a href="{{ route('second', ['pages', 'invoice']) }}" class="dropdown-item">Invoice</a>
                            <a href="{{ route('second', ['pages', 'faq']) }}" class="dropdown-item">FAQ</a>
                            <a href="{{ route('second', ['pages', 'pricing']) }}" class="dropdown-item">Pricing</a>
                            <a href="{{ route('second', ['pages', 'maintenance']) }}" class="dropdown-item">Maintenance</a>
                        </div>
                    </li> --}}

                     {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('quotations.index')}}"  role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pages-line"></i>ใบเสนอราคา
                        </a>
                     </li> --}}
                      
                   
                    
                    <li class="nav-item dropdown" >
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('customers.index')}}">
                            <i class="ri-briefcase-line"></i>ข้อมูลลูกค้า 
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pages-line"></i>ใบเสนอราคา <i class="mdi mdi-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{route('quotations.index')}}" class="dropdown-item">
                                <i class="ri-file-list-line me-2"></i>ทั้งหมด
                            </a>
                            <a href="{{route('quotations.pending')}}" class="dropdown-item">
                                <i class="ri-time-line me-2 text-warning"></i>รออนุมัติ
                            </a>
                            <a href="{{route('quotations.approved')}}" class="dropdown-item">
                                <i class="ri-checkbox-circle-line me-2 text-success"></i>อนุมัติแล้ว
                            </a>
                            <a href="{{route('quotations.cancelled')}}" class="dropdown-item">
                                <i class="ri-close-circle-line me-2 text-danger"></i>ไม่อนุมัติ
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('orders.index')}}">
                            <i class="ri-numbers-line"></i>คำสั่งซื้อ
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-bar-chart-box-line"></i>รายงาน <i class="mdi mdi-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{route('reports.sales')}}" class="dropdown-item">
                                <i class="ri-user-star-line me-2"></i>สถิติการขายของ Sale
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('scan.invoice')}}">
                            <i class="ri-qr-code-line"></i>ScanQrcode
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('payments.confirm')}}">
                            <i class="ri-money-dollar-box-line"></i>อนุมัติยอด
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('deliveries.calendar')}}">
                            <i class="ri-calendar-event-line"></i>ปฏิทินจัดส่งสินค้า
                        </a>
                    </li>
                       
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-layouts" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-layout-line"></i>ตั้งค่าระบบ <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-layouts">
                            <a href="{{ route('products.index') }}" class="dropdown-item">สิ้นค้าทั้งหมด</a>
                            <a href="{{ route('global-sets.index') }}" class="dropdown-item">GlobalSets</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <a href="{{ route('users.index') }}" class="dropdown-item">จัดการผู้ใช้งาน</a>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->