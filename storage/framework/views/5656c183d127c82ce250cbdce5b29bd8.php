<!DOCTYPE html>
<html lang="en" data-sidenav-size="<?php echo e($sidenav ?? 'default'); ?>" data-layout-mode="<?php echo e($layoutMode ?? 'fluid'); ?>" data-layout-position="<?php echo e($position ?? 'fixed'); ?>" data-menu-color="<?php echo e($menuColor ?? 'dark'); ?>" data-topbar-color="<?php echo e($topbarColor ?? 'light'); ?>">

<head>
    <?php echo $__env->make('layouts.shared/title-meta', ['title' => $title], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('css'); ?>
    <?php echo $__env->make('layouts.shared/head-css', ['mode' => $mode ?? '', 'demo' => $demo ?? ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php echo $__env->make('layouts.shared/topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('layouts.shared/left-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <?php echo $__env->make('layouts.shared/footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

    </div>
    <!-- END wrapper -->

    <?php echo $__env->yieldContent('modal'); ?>

    <?php echo $__env->make('layouts.shared/right-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('layouts.shared/footer-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/layout.js', 'resources/js/main.js']); ?>

</body>

</html>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/layouts/vertical.blade.php ENDPATH**/ ?>