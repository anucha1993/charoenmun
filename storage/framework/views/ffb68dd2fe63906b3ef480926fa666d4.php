<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.shared/page-title', ['sub_title' => 'Base UI', 'page_title' => 'Progress'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Examples</h4>
                    <p class="text-muted mb-0">A progress bar can be used to show a user how far along he/she is in a process.</p>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>  
                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Height</h4>
                    <p class="text-muted mb-0">We only set a <code>height</code> value on the <code>.progress</code>, so if you change that value the inner <code>.progress-bar</code> will automatically resize accordingly.
                        Use <code>.progress-sm</code>,<code>.progress-md</code>,<code>.progress-lg</code>,<code>.progress-xl</code> classes.
                    </p>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 1px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2" style="height: 3px;">
                        <div class="progress-bar" role="progressbar" style="width: 25%; height: 20px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2 progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2 progress-md">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress progress-lg mb-2">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress progress-xl">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 38%" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Multiple bars</h4>
                    <p class="text-muted mb-0">Include multiple progress bars in a progress component if you need.</p>
                </div>
                <div class="card-body">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div> 
                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Animated stripes</h4>
                    <p class="text-muted mb-0">The striped gradient can also be animated. Add <code>.progress-bar-animated</code> to <code>.progress-bar</code> to animate the stripes right to left via CSS3 animations.</p>
                </div>
                <div class="card-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                    </div>  
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col -->
    
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Labels</h4>
                    <p class="text-muted mb-0">Add labels to your progress bars by placing text within the <code>.progress-bar</code>.</p>
                </div>
                <div class="card-body">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Backgrounds</h4>
                    <p class="text-muted mb-0">Use background utility classes to change the appearance of individual progress bars.</p>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-pink" role="progressbar" style="width: 85%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-purple" role="progressbar" style="width: 85%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-dark" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>      
                    <div class="progress">
                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>   
                </div> <!-- end card-body -->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Striped</h4>
                    <p class="text-muted mb-0">Add <code>.progress-bar-striped</code> to any <code>.progress-bar</code> to apply a stripe via CSS gradient over the progress bar’s background color.</p>
                </div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div> 
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-pink" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card-->

        </div> <!-- end col -->
    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'Progress', 'mode' => $mode ?? '', 'demo' => $demo ?? ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\charoenmun\resources\views/ui/progress.blade.php ENDPATH**/ ?>