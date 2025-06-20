<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.shared/page-title', ['sub_title' => 'Base UI', 'page_title' => 'Avatars'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Sizing - Images</h4>
                    <p class="text-muted mb-0">
                        Create and group avatars of different sizes and shapes with the css classes.
                        Using Bootstrap's naming convention, you can control size of avatar including standard avatar, or scale it up to different sizes.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="/images/users/avatar-2.jpg" alt="image"
                                class="img-fluid avatar-xs rounded">
                            <p>
                                <code>.avatar-xs</code>
                            </p>
                            <img src="/images/users/avatar-3.jpg" alt="image"
                                class="img-fluid avatar-sm rounded mt-2">
                            <p class="mb-2 mb-sm-0">
                                <code>.avatar-sm</code>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <img src="/images/users/avatar-4.jpg" alt="image"
                                class="img-fluid avatar-md rounded" />
                            <p>
                                <code>.avatar-md</code>
                            </p>
                        </div>

                        <div class="col-md-3">
                            <img src="/images/users/avatar-5.jpg" alt="image"
                                class="img-fluid avatar-lg rounded" />
                            <p>
                                <code>.avatar-lg</code>
                            </p>
                        </div>

                        <div class="col-md-3">
                            <img src="/images/users/avatar-6.jpg" alt="image"
                                class="img-fluid avatar-xl rounded" />
                            <p class="mb-0">
                                <code>.avatar-xl</code>
                            </p>
                        </div>
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Rounded Circle</h4>
                    <p class="text-muted mb-0">
                        Using an additional class <code>.rounded-circle</code> in
                        <code>&lt;img&gt;</code> element creates the rounded avatar.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/images/users/avatar-7.jpg" alt="image"
                                class="img-fluid avatar-md rounded-circle" />
                            <p class="mt-1">
                                <code>.avatar-md .rounded-circle</code>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <img src="/images/users/avatar-8.jpg" alt="image"
                                class="img-fluid avatar-lg rounded-circle" />
                            <p>
                                <code>.avatar-lg .rounded-circle</code>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <img src="/images/users/avatar-9.jpg" alt="image"
                                class="img-fluid avatar-xl rounded-circle" />
                            <p class="mb-0">
                                <code>.avatar-xl .rounded-circle</code>
                            </p>
                        </div>
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Sizing - Background Color</h4>
                    <p class="text-muted mb-0">
                        Using utilities classes of background e.g. <code>bg-*</code> allows you to have
                        any background color as well.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="avatar-xs">
                                <span class="avatar-title rounded">
                                    xs
                                </span>
                            </div>
                            <p class="mb-2 fs-14 mt-1">
                                Using <code>.avatar-xs</code>
                            </p>

                            <div class="avatar-sm mt-3">
                                <span class="avatar-title bg-success rounded">
                                    sm
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-sm</code>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="avatar-md">
                                <span class="avatar-title bg-info-subtle text-info fs-20 rounded">
                                    MD
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-md</code>
                            </p>
                        </div>

                        <div class="col-md-3">
                            <div class="avatar-lg">
                                <span class="avatar-title bg-danger fs-22 rounded">
                                    LG
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-lg</code>
                            </p>
                        </div>

                        <div class="col-md-3">
                            <div class="avatar-xl">
                                <span class="avatar-title bg-warning-subtle text-warning fs-24 rounded">
                                    XL
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-xl</code>
                            </p>
                        </div>
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Rounded Circle Background</h4>
                    <p class="text-muted mb-0">
                        Using an additional class <code>.rounded-circle</code> in
                        <code>&lt;img&gt;</code> element creates the rounded avatar.
                    </p>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="avatar-md">
                                <span class="avatar-title bg-secondary-subtle text-secondary fs-20 rounded-circle">
                                    MD
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-md .rounded-circle</code>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <div class="avatar-lg">
                                <span class="avatar-title bg-light text-dark fs-22 rounded-circle">
                                    LG
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-lg .rounded-circle</code>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <div class="avatar-xl">
                                <span
                                    class="avatar-title bg-primary-subtle text-primary fs-24 rounded-circle">
                                    XL
                                </span>
                            </div>

                            <p class="mb-0 fs-14 mt-1">
                                Using <code>.avatar-xl .rounded-circle</code>
                            </p>
                        </div>
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Images Shapes</h4>
                    <p class="text-muted mb-0">
                        Avatars with different sizes and shapes.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="/images/small/small-2.jpg" alt="image"
                                class="img-fluid rounded" width="200" />
                            <p class="mb-0">
                                <code>.rounded</code>
                            </p>
                        </div>

                        <div class="col-sm-2 text-center">
                            <img src="/images/users/avatar-6.jpg" alt="image"
                                class="img-fluid rounded" width="120" />
                            <p class="mb-0">
                                <code>.rounded</code>
                            </p>
                        </div>

                        <div class="col-sm-2 text-center">
                            <img src="/images/users/avatar-7.jpg" alt="image"
                                class="img-fluid rounded-circle" width="120" />
                            <p class="mb-0">
                                <code>.rounded-circle</code>
                            </p>
                        </div>

                        <div class="col-sm-2">
                            <img src="/images/small/small-3.jpg" alt="image"
                                class="img-fluid img-thumbnail" width="200" />
                            <p class="mb-0">
                                <code>.img-thumbnail</code>
                            </p>
                        </div>
                        <div class="col-sm-2">
                            <img src="/images/users/avatar-8.jpg" alt="image"
                                class="img-fluid rounded-circle img-thumbnail" width="120" />
                            <p class="mb-0">
                                <code>.rounded-circle .img-thumbnail</code>
                            </p>
                        </div>
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Avatar Group</h4>
                    <p class="text-muted mb-0">
                        Use <code>avatar-group</code> class to show avatar
                        images with the group. Use <code>avatar-group</code> class with
                        <code>data-bs-toggle="tooltip"</code> to show avatar group images
                        with tooltip.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mt-lg-0 mt-3">
                                <div class="avatar-group">
                                    <div class="avatar-group-item">
                                        <img src="/images/users/avatar-4.jpg" alt=""
                                            class="rounded-circle avatar-xs">
                                    </div>
                                    <div class="avatar-group-item">
                                        <img src="/images/users/avatar-5.jpg" alt=""
                                            class="rounded-circle avatar-xs">
                                    </div>
                                    <div class="avatar-group-item">
                                        <div class="avatar-xs">
                                            <div class="avatar-title rounded-circle text-bg-info">
                                                A
                                            </div>
                                        </div>
                                    </div>
                                    <div class="avatar-group-item">
                                        <img src="/images/users/avatar-2.jpg" alt=""
                                            class="rounded-circle avatar-xs">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-lg-6">
                            <div class="mt-lg-0 mt-3">
                                <div class="avatar-group">
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Tosha">
                                        <img src="/images/users/avatar-1.jpg" alt=""
                                            class="rounded-circle avatar-sm">
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Brain">
                                        <img src="/images/users/avatar-3.jpg" alt=""
                                            class="rounded-circle avatar-sm">
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hooker">
                                        <div class="avatar-sm">
                                            <div
                                                class="avatar-title rounded-circle bg-light text-primary">
                                                K
                                            </div>
                                        </div>
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="More +">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle">
                                                9+
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div><!-- end card body -->
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.vertical', ['title' => 'Avatars', 'mode' => $mode ?? '', 'demo' => $demo ?? ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\charoenmun\resources\views/ui/avatars.blade.php ENDPATH**/ ?>