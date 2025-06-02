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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- <<<  เปลี่ยนจาก @yield เป็น @stack  >>> --}}
    @stack('css')

    <script>
      // กำหนดค่า config ถาวร
      sessionStorage.setItem('__CONFIG__', JSON.stringify({
          theme:'light',
          nav:'topnav',
          layout:{mode:'fluid',position:'fixed'},
          topbar:{color:'dark'},
          menu:{color:'light'},
          sidenav:{size:'default',user:false}
      }));
    </script>

    {{-- head-css --}}
    @include('layouts.shared.head-css', ['mode' => $mode, 'demo' => $demo])

    {{-- Livewire Styles --}}
    @livewireStyles
</head>

<body>

    <div class="wrapper">

        @include('layouts.shared.topbar')
        @include('layouts.shared.horizontal-nav')

         <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">
                    {{ $slot }}
                </div>
                <!-- container -->

            </div>
            <!-- content -->

            @include('layouts.shared/footer')
        </div>
    </div>

    {{-- modal area --}}
    @isset($modal)
        {{ $modal }}
    @endisset

    @include('layouts.shared.right-sidebar')
    @include('layouts.shared.footer-scripts')

    {{-- ตรงนี้แก้ไขให้ @livewireScripts มาก่อน @stack('scripts') --}}
    @livewireScripts
    @stack('scripts')

    {{-- Vite / Mix JS --}}
    @vite(['resources/js/layout.js', 'resources/js/main.js'])

    {{-- Toastr + jQuery (ถ้าจำเป็น) --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    {{-- Livewire Notify Event --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({ message, type = 'success' }) => {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "3000"
                };

                if (type === 'error') {
                    toastr.error(message);
                } else if (type === 'warning') {
                    toastr.warning(message);
                } else if (type === 'info') {
                    toastr.info(message);
                } else {
                    toastr.success(message);
                }
            });
        });
    </script>
</body>
</html>
