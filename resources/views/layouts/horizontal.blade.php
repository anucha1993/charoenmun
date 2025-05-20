{{-- resources/views/layouts/horizontal.blade.php --}}
@props([
    'title'        => 'Dashboard',
    'menuColor'    => 'light',
    'topbarColor'  => 'light',
    'mode'         => '',
    'demo'         => '',
])

<!DOCTYPE html>
<html lang="en"
      data-layout="topnav"
      data-menu-color="{{ $menuColor }}"
      data-topbar-color="{{ $topbarColor }}">

<head>
    {{-- title / meta --}}
    @include('layouts.shared.title-meta', ['title' => $title])

    {{-- <<<  เปลี่ยนจาก @yield เป็น @stack  >>> --}}
    @stack('css')
    {{-- <script>
      // แบบกำหนดเองถาวร:
      sessionStorage.setItem('__CONFIG__', JSON.stringify({
          theme:'light',
          nav:'topnav',
          layout:{mode:'fluid',position:'fixed'},
          topbar:{color:'dark'},
          menu:{color:'light'},
          sidenav:{size:'default',user:false}
      }));
      // หรือจะใช้ removeItem('__CONFIG__') ถ้าอยากให้ยึดค่าจาก Blade
    </script> --}}

    {{-- head-css --}}
    @include('layouts.shared.head-css', ['mode' => $mode, 'demo' => $demo])
    @livewireStyles
</head>

<body>
    <div class="wrapper">

        @include('layouts.shared.topbar')
        @include('layouts.shared.horizontal-nav')

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">


                    {{ $slot }}
                </div>
            </div>

            @include('layouts.shared.footer')
        </div>
    </div>

    {{-- modal area --}}
    @isset($modal)
        {{ $modal }}
    @endisset

    @include('layouts.shared.right-sidebar')
    @include('layouts.shared.footer-scripts')

    {{-- ตรงนี้ยังใช้ @stack('scripts') ได้ --}}
{{-- ★ เพิ่มสองบรรทัดนี้ ★ --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    @stack('scripts')

    

    @vite(['resources/js/layout.js', 'resources/js/main.js'])
 
    @livewireScripts
</body>
</html>
