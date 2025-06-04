<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('any', 'index')}}" id="topnav-dashboards" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-dashboard-3-line"></i>Dashboards
                        </a>
                    </li>
                    <li class="nav-item dropdown">
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
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="{{route('customers.index')}}">
                            <i class="ri-briefcase-line"></i>ข้อมูลลูกค้า 
                        </a>
                       
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-layouts" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-layout-line"></i>ตั้งค่าระบบ <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-layouts">
                            <a href="{{ route('products.index') }}" class="dropdown-item" >สิ้นค้าทั้งหมด</a>
                            <a href="{{ route('global-sets.index') }}" class="dropdown-item" >GlobalSets</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->