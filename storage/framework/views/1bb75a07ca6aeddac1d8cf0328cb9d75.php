<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.shared/page-title', ['sub_title' => 'Base UI', 'page_title' => 'Badges'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Default</h4>
                    <p class="text-muted mb-0">
                        A simple labeling component. Badges scale to match the size of the immediate parent element by using relative font sizing and <code>em</code> units.
                    </p>
                </div>
                <div class="card-body">
                    <h1>h1.Example heading <span class="badge bg-secondary text-light">New</span></h1>
                    <h2>h2.Example heading <span class="badge bg-success-subtle text-success">New</span></h2>
                    <h3>h2.Example heading <span class="badge bg-primary">New</span></h3>
                    <h4>h4.Example heading <a href="#" class="badge bg-info-subtle text-info">Info Link</a></h4>
                    <h5>h5.Example heading <span class="badge badge-outline-warning">New</span></h5>
                    <h6>h6.Example heading <span class="badge bg-danger">New</span></h6>

                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Pill Badges</h4>
                    <p class="text-muted fs-14 mb-3">
                        Use the <code>.rounded-pill</code> modifier class to make badges more rounded.
                    </p>

                    <span class="badge bg-primary rounded-pill">Primary</span>
                    <span class="badge bg-secondary text-light rounded-pill">Secondary</span>
                    <span class="badge bg-success rounded-pill">Success</span>
                    <span class="badge bg-danger rounded-pill">Danger</span>
                    <span class="badge bg-warning rounded-pill">Warning</span>
                    <span class="badge bg-info rounded-pill">Info</span>
                    <span class="badge text-bg-light rounded-pill">Light</span>
                    <span class="badge text-bg-dark rounded-pill">Dark</span>

                    <h5 class="mt-4">Lighten Badges</h5>
                    <p class="text-muted fs-14 mb-3">
                        Use the <code>.bg-*-subtle text-*</code> modifier class to make badges lighten.
                    </p>

                    <span class="badge bg-primary-subtle text-primary rounded-pill">Primary</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">Secondary</span>
                    <span class="badge bg-success-subtle text-success rounded-pill">Success</span>
                    <span class="badge bg-danger-subtle text-danger rounded-pill">Danger</span>
                    <span class="badge bg-warning-subtle text-warning rounded-pill">Warning</span>
                    <span class="badge bg-info-subtle text-info rounded-pill">Info</span>
                    <span class="badge bg-dark-subtle text-dark rounded-pill">Dark</span>

                    <h5 class="mt-4">Outline Badges</h5>
                    <p class="text-muted fs-14 mb-3">
                        Using the <code>.badge-outline-*</code> to quickly create a bordered badges.
                    </p>

                    <span class="badge badge-outline-primary rounded-pill">Primary</span>
                    <span class="badge badge-outline-secondary rounded-pill">Secondary</span>
                    <span class="badge badge-outline-success rounded-pill">Success</span>
                    <span class="badge badge-outline-danger rounded-pill">Danger</span>
                    <span class="badge badge-outline-warning rounded-pill">Warning</span>
                    <span class="badge badge-outline-info rounded-pill">Info</span>
                    <span class="badge badge-outline-dark rounded-pill">Dark</span>

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end col-->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Contextual variations</h4>
                    <p class="text-muted fs-14 mb-3">
                        Add any of the below mentioned modifier classes to change the appearance of a badge.
                        Badge can be more contextual as well. Just use regular convention e.g. <code>badge-*color</code>, <code>bg-primary</code>
                        to have badge with different background.
                    </p>

                    <span class="badge bg-primary">Primary</span>
                    <span class="badge bg-secondary text-light">Secondary</span>
                    <span class="badge bg-success">Success</span>
                    <span class="badge bg-danger">Danger</span>
                    <span class="badge bg-warning">Warning</span>
                    <span class="badge bg-info">Info</span>
                    <span class="badge bg-pink">Pink</span>
                    <span class="badge bg-purple">Purple</span>
                    <span class="badge bg-light text-dark">Light</span>
                    <span class="badge bg-dark text-light">Dark</span>

                    <h5 class="mt-4">Lighten Badges</h5>
                    <p class="text-muted fs-14 mb-3">
                        Using the <code>.bg-*-subtle text-*</code> modifier class, you can have more soften variation.
                    </p>

                    <span class="badge bg-primary-subtle text-primary">Primary</span>
                    <span class="badge bg-secondary-subtle text-secondary">Secondary</span>
                    <span class="badge bg-success-subtle text-success">Success</span>
                    <span class="badge bg-danger-subtle text-danger">Danger</span>
                    <span class="badge bg-warning-subtle text-warning">Warning</span>
                    <span class="badge bg-info-subtle text-info">Info</span>
                    <span class="badge bg-dark-subtle text-dark">Dark</span>
                    <span class="badge bg-pink-subtle text-pink">Pink</span>
                    <span class="badge bg-purple-subtle text-purple">Purple</span>

                    <h5 class="mt-4">Outline Badges</h5>
                    <p class="text-muted fs-14 mb-3">
                        Using the <code>.badge-outline-*</code> to quickly create a bordered badges.
                    </p>

                    <span class="badge badge-outline-primary">Primary</span>
                    <span class="badge badge-outline-secondary">Secondary</span>
                    <span class="badge badge-outline-success">Success</span>
                    <span class="badge badge-outline-danger">Danger</span>
                    <span class="badge badge-outline-warning">Warning</span>
                    <span class="badge badge-outline-info">Info</span>
                    <span class="badge badge-outline-dark">Dark</span>

                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Badge Positioned</h4>
                    <p class="text-muted mb-0">
                        Use utilities to modify a <code>.badge</code> and position it in the corner of a
                        link or button.
                    </p>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary position-relative">
                                Inbox
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    99+
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-primary position-relative">
                                Profile
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                  <span class="visually-hidden">New alerts</span>
                                </span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success mt-4">
                                Notifications <span class="badge bg-light text-dark ms-1">4</span>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div> <!-- end col -->
    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.vertical', ['title' => 'Badges', 'mode' => $mode ?? '', 'demo' => $demo ?? ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\charoenmun\resources\views/ui/badges.blade.php ENDPATH**/ ?>