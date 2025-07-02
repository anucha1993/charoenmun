
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title'        => 'Dashboard',
    'menuColor'    => 'light',
    'topbarColor'  => 'light',
    'mode'         => '',
    'demo'         => '',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title'        => 'Dashboard',
    'menuColor'    => 'light',
    'topbarColor'  => 'light',
    'mode'         => '',
    'demo'         => '',
]); ?>
<?php foreach (array_filter(([
    'title'        => 'Dashboard',
    'menuColor'    => 'light',
    'topbarColor'  => 'light',
    'mode'         => '',
    'demo'         => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<!DOCTYPE html>
<html lang="en"
      data-layout="topnav"
      data-menu-color="<?php echo e($menuColor); ?>"
      data-topbar-color="<?php echo e($topbarColor); ?>">

<head>
    
    <?php echo $__env->make('layouts.shared.title-meta', ['title' => $title], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    
    <?php echo $__env->yieldPushContent('css'); ?>
   <script>
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
    </script> 

    
    <?php echo $__env->make('layouts.shared.head-css', ['mode' => $mode, 'demo' => $demo], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body>

    <div class="wrapper">



         <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">
                    <br>
                    <?php echo e($slot); ?>

                </div>
                <!-- container -->

            </div>
            <!-- content -->

        </div>
    </div>

    
    <?php if(isset($modal)): ?>
        <?php echo e($modal); ?>

    <?php endif; ?>

    <?php echo $__env->make('layouts.shared.right-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('layouts.shared.footer-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    



<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>




    <?php echo $__env->yieldPushContent('scripts'); ?>

    

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/layout.js', 'resources/js/main.js']); ?>
 
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


<script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', ({
                message,
                type = 'success'
            }) => {
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
<?php /**PATH C:\laragon\www\charoenmun\resources\views/layouts/horizontal-print.blade.php ENDPATH**/ ?>