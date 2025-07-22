<div>
    <style>

    </style>

    <div class="content-page">
        <div class="content">

            <div class="col-12">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">

                            </div>
                            <h1 class="page-title" style="font-size: 25px">รายการสินค้าทั้งหมด</h1>
                        </div>
                    </div>
                </div>

                <div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const modal = new bootstrap.Modal(document.getElementById('product-modal'));

                            window.addEventListener('open-modal', () => modal.show());
                            window.addEventListener('close-modal', () => modal.hide());
                        });
                    </script>

                    
                    <div class="row align-items-center g-2 mb-3">

                        
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>


                                <input type="text" class="form-control" placeholder="ค้นหาด้วยชื่อหรือรหัสสินค้า…"
                                    wire:model.live.debounce.500ms="search">
                            </div>
                        </div>

                        
                        <div class="col-auto ms-auto">
                            <button class="btn btn-primary" wire:click="resetForm"
                                onclick="window.dispatchEvent(new Event('open-modal'))">
                                <i class="ri-add-line me-1"></i> เพิ่มสินค้า
                            </button>
                        </div>
                    </div>

                    
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="mb-0">รายการสินค้า</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-centered mb-0" id="inline-editable" style="font-size: 18px">
                                <thead class="">
                                    <tr>
                                        <th style="width:60px;">IDs</th>
                                        <th>รหัส</th>
                                        <th>ชื่อสินค้า</th>
                                        <th class="text-end">น้ำหนัก (kg)</th>
                                        <th class="text-end">ราคา (฿)</th>
                                        <th>ประเภท</th>
                                        <th>หน่วย</th>
                                        <th>มาตราวัด</th>
                                        <th>สถานะ</th>
                                        <th style="width:130px;" class="text-center">จัดการ</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr wire:key="row-<?php echo e($p->product_id); ?>">
                                            <td><?php echo e($products->firstItem() + $i); ?></td>
                                            <td><?php echo e($p->product_code); ?></td>
                                            <td><?php echo e($p->product_name."(".$p->product_size.")".' '.$p->productWireType?->value); ?></td>
                                            <td class="text-end"><?php echo e(number_format($p->product_weight).'.Kg/เมตร'); ?></td>
                                            <td class="text-end"><?php echo e(number_format($p->product_price, 2)); ?></td>
                                            <td><?php echo e($p->productType->value?? 'ไม่ระบุ'); ?></td>
                                            <td><?php echo e($p->productUnit->value); ?></td>
                                            <td><?php echo e($p->productMeasure->value); ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo e($p->product_status ? 'success' : 'secondary'); ?>">
                                                    <?php echo e($p->product_status ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning me-1"
                                                    wire:click="edit(<?php echo e($p->product_id); ?>)">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirm('ยืนยันลบสินค้า?') || event.stopImmediatePropagation()"
                                                    wire:click="destroy(<?php echo e($p->product_id); ?>)">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">— ไม่พบข้อมูล —</td>
                                        </tr>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            
                            <?php echo e($products->links('pagination::bootstrap-5')); ?>


                        </div>
                    </div>

                    
                    <div wire:ignore.self class="modal fade" id="product-modal" tabindex="-1"
                        aria-labelledby="productModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                
                                <div class="modal-header">
                                    <h4 class="modal-title" id="productModalLabel">
                                        <?php echo e($isEdit ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า'); ?>

                                    </h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                
                                <form wire:submit.prevent="<?php echo e($isEdit ? 'update' : 'store'); ?>">
                                    <div class="modal-body">
                                        <div class="row g-1">
                                            
                                            <div class="col-md-6">
                                                <label class="form-label">รหัสสินค้า *</label>
                                                <input type="text" wire:model.defer="product_code"  style="background-color: aliceblue"
                                                    placeholder="รหัสสินค้า"
                                                    class="form-control <?php $__errorArgs = ['product_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="form-label">ชื่อสินค้า *</label>
                                                <input type="text" wire:model.defer="product_name"required
                                                    placeholder="ชื่อสินค้า" 
                                                    class="form-control <?php $__errorArgs = ['product_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                             <div class="col-md-12">
                                                <label class="form-label">ขนาดสินค้าสินค้า *</label>
                                                <input type="text" wire:model.defer="product_size" placeholder="กว้าง x ยาว x สูง...." required
                                                    placeholder="ชื่อสินค้า"
                                                    class="form-control <?php $__errorArgs = ['product_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label">ประเภทสินค้า *</label>
                                                <select wire:model.defer="product_type" class="form-select <?php $__errorArgs = ['product_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                                     <option value="" >---กรุณาเลือก---</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $productType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                                            </div>

                                             <div class="col-md-6">
                                                <label class="form-label">ประเภท ลวด *</label>
                                                <select wire:model.defer="product_wire_type" class="form-select <?php $__errorArgs = ['product_wire_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value="0">ไม่เลือก</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $productWireType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_wire_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                             <div class="col-md-6">
                                                <label class="form-label">ประเภทเหล็กข้าง *</label>
                                                <select wire:model.defer="product_side_steel_type" class="form-select <?php $__errorArgs = ['product_side_steel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value="0">ไม่เลือก</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $productSideSteelType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_side_steel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            

                                            
                                            <div class="col-md-2">
                                                <label class="form-label">น้ำหนัก (kg)</label>
                                                <input type="number" step="0.01" wire:model.defer="product_weight" placeholder=".Kg"
                                                    class="form-control <?php $__errorArgs = ['product_weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                               <div class="col-md-2">
                                                <label class="form-label">ความยาว</label>
                                                <input type="number" step="0.01" wire:model.defer="product_length" placeholder="20"
                                                    class="form-control <?php $__errorArgs = ['product_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                              <div class="col-md-2">
                                                <label class="form-label">มาตราวัด</label>
                                                 <select wire:model.defer="product_measure" class="form-select <?php $__errorArgs = ['product_measure'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value="0">ไม่เลือก</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $productMeasure; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <label class="form-label">ราคา (฿) *</label>
                                                <input type="number" step="0.01" wire:model.defer="product_price"
                                                    class="form-control <?php $__errorArgs = ['product_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>



                                            
                                            <div class="col-md-6">
                                                <label class="form-label">หน่วย *</label>

                                                 <select wire:model.defer="product_unit" class="form-select <?php $__errorArgs = ['product_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                     <option value="0">ไม่เลือก</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $productUnit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->value); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>

                                               
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label">ความหนา (ระบุหากต้องการใช้คำนวน)</label>
                                                <input type="number" step="0.01" wire:model.defer="product_calculation" placeholder="ระบุหากต้องการใช้คำนวน"
                                                    class="form-control <?php $__errorArgs = ['product_calculation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['product_calculation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            
                                            <div class="col-12">
                                                <label class="form-label">หมายเหตุ</label>
                                                <textarea rows="6" wire:model.defer="product_note" class="form-control"></textarea>
                                            </div>

                                            
                                            <div class="col-12 mt-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="statusSwitch" wire:model.defer="product_status">
                                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="submit" class="btn btn-success">
                                            <?php echo e($isEdit ? 'อัปเดต' : 'บันทึก'); ?>

                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>






            </div>

            
            <?php $__env->startPush('scripts'); ?>
                <script>
                    window.addEventListener('notify', e => {
                        const {
                            type = 'success', text = ''
                        } = e.detail;
                        toastr.options = {
                            timeOut: 3500,
                            progressBar: true,
                            positionClass: 'toast-top-right'
                        };
                        toastr[type](text);
                    });
                </script>
            <?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/products/product-index.blade.php ENDPATH**/ ?>