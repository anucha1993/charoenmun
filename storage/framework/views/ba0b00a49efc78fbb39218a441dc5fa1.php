
<div>
    <style>
        /* Flowaccount-inspired clean design */
        .page-container {
            background: #f8fafc;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px 32px;
            margin-bottom: 24px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-subtitle {
            font-size: 16px;
            color: #6b7280;
            margin: 8px 0 0 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
            padding: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }
        
        .stat-content {
            position: relative;
            z-index: 2;
        }
        
        .stat-content h3 {
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-content p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
            margin: 6px 0 0 0;
            font-weight: 500;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .stat-icon.primary { background: rgba(255, 255, 255, 0.2); color: white; }
        .stat-icon.warning { background: rgba(255, 255, 255, 0.2); color: white; }
        .stat-icon.success { background: rgba(255, 255, 255, 0.2); color: white; }
        .stat-icon.danger { background: rgba(255, 255, 255, 0.2); color: white; }
        
        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .filter-row {
            display: flex;
            gap: 16px;
            align-items: end;
        }
        
        .filter-col {
            flex: 1;
        }
        
        .data-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            background: #f9fafb;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .clean-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .clean-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 18px 24px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .clean-table td {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
            vertical-align: middle;
        }
        
        .clean-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .clean-table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: scale(1.001);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .quote-number {
            font-weight: 700;
            color: #3b82f6;
            font-size: 15px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #93c5fd;
            display: inline-block;
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
        }
        
        .customer-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
            font-size: 15px;
        }
        
        .customer-phone {
            font-size: 13px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .customer-address {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .amount {
            font-weight: 700;
            color: #059669;
            font-size: 17px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #6ee7b7;
            display: inline-block;
        }
        
        .seller-info {
            display: flex;
            align-items: center;
        }
        
        .seller-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 12px;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .btn-icon:hover::before {
            left: 100%;
        }
        
        .btn-icon:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        
        .btn-icon.danger:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        
        .form-control, .form-select {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #2563eb;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }
        
        .pagination-wrapper {
            padding: 20px 24px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #d1d5db;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-content h3 {
                font-size: 24px;
            }
            
            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 24px;
            }
            
            .filter-row {
                flex-direction: column;
                gap: 12px;
            }
            
            .clean-table {
                font-size: 12px;
            }
            
            .clean-table th,
            .clean-table td {
                padding: 12px 16px;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .page-title {
                font-size: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .clean-table th:nth-child(5),
            .clean-table td:nth-child(5) {
                display: none;
            }
            
            .customer-address {
                display: none;
            }
        }
    </style>

    <div class="page-container">
        <div class="content-wrapper">
            
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">ใบเสนอราคา</h1>
                        <p class="page-subtitle">จัดการและติดตามใบเสนอราคาทั้งหมด</p>
                    </div>
                    <a href="<?php echo e(route('quotations.create')); ?>" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>สร้างใบเสนอราคา
                    </a>
                </div>
            </div>

            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <h3><?php echo e($quotes->total()); ?></h3>
                        <p>ใบเสนอราคาทั้งหมด</p>
                    </div>
                    <div class="stat-icon primary">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <h3><?php echo e($quotes->where('quote_status', 'wait')->count()); ?></h3>
                        <p>รอดำเนินการ</p>
                    </div>
                    <div class="stat-icon warning">
                        <i class="ri-time-line"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <h3><?php echo e($quotes->where('quote_status', 'success')->count()); ?></h3>
                        <p>ยืนยันแล้ว</p>
                    </div>
                    <div class="stat-icon success">
                        <i class="ri-check-line"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <h3>฿<?php echo e(number_format($quotes->sum('quote_grand_total'), 0)); ?></h3>
                        <p>มูลค่ารวมทั้งหมด</p>
                    </div>
                    <div class="stat-icon success">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                </div>
            </div>

            
            <div class="filter-card">
                <div class="filter-row">
                    <div class="filter-col">
                        <label class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">
                            <i class="ri-search-line me-1"></i>ค้นหา
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="ค้นหาเลขที่ใบเสนอราคา, ชื่อลูกค้า, เบอร์โทร..."
                            wire:model.debounce.500ms.live="search"
                            style="padding: 12px 16px; font-size: 14px;"
                        >
                    </div>
                    <div class="filter-col" style="flex: 0 0 200px;">
                        <label class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">
                            <i class="ri-filter-line me-1"></i>สถานะ
                        </label>
                        <select class="form-select" wire:model.live="status" style="padding: 12px 16px; font-size: 14px;">
                            <option value="">ทั้งหมด</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>">
                                    <!--[if BLOCK]><![endif]--><?php if($s === 'wait'): ?> 🕐 รอดำเนินการ
                                    <?php elseif($s === 'success'): ?> ✅ ยืนยันแล้ว
                                    <?php else: ?> ❌ ยกเลิก
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    <div class="filter-col" style="flex: 0 0 120px;">
                        <label class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">
                            &nbsp;
                        </label>
                        <button class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <i class="ri-refresh-line me-1"></i>รีเฟรช
                        </button>
                    </div>
                </div>
            </div>

            
            <div class="data-table">
                <div class="table-header">
                    <h2 class="table-title">รายการใบเสนอราคา</h2>
                </div>
                
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>เลขที่</th>
                            <th>สถานะ</th>
                            <th>วันที่</th>
                            <th>ลูกค้า</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th style="text-align: right;">จำนวนเงิน</th>
                            <th>ผู้ขาย</th>
                            <th style="text-align: center;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="quote-number"><?php echo e($q->quote_number); ?></span>
                                </td>
                                <td>
                                    <?php echo quote_status_badge($q->quote_status); ?>

                                </td>
                                <td>
                                    <div>
                                        <div style="font-weight: 600; color: #111827;"><?php echo e($q->quote_date->format('d/m/Y')); ?></div>
                                        <div style="font-size: 12px; color: #6b7280;"><?php echo e($q->quote_date->format('H:i น.')); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name"><?php echo e($q->customer->customer_name); ?></div>
                                        <div class="customer-phone"><?php echo e($q->customer->customer_phone); ?></div>
                                        <div class="customer-address" title="<?php echo e($q->customer->customer_address); ?>">
                                            <?php echo e($q->customer->customer_address); ?>

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; color: #374151; max-width: 180px;">
                                        <div style="font-weight: 600; color: #059669;">
                                            <?php echo e(optional($q->deliveryAddress)->delivery_contact_name ?? $q->customer->customer_name); ?>

                                        </div>
                                        <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                                            <?php echo e(optional($q->deliveryAddress)->delivery_phone ?? $q->customer->customer_phone); ?>

                                        </div>
                                        <div style="font-size: 12px; color: #9ca3af; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                             title="<?php echo e(optional($q->deliveryAddress)->delivery_address ?? $q->customer->customer_address); ?>">
                                            <?php echo e(optional($q->deliveryAddress)->delivery_address ?? $q->customer->customer_address); ?>

                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <span class="amount">฿<?php echo e(number_format($q->quote_grand_total, 2)); ?></span>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                        <?php echo e($q->items->count()); ?> รายการ
                                    </div>
                                </td>
                                <td>
                                    <div class="seller-info">
                                      
                                        <div>
                                            <div style="font-weight: 600; color: #111827;"><?php echo e($q->sale->name); ?></div>
                                            <div style="font-size: 11px; color: #6b7280;">Sale</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="<?php echo e(route('quotations.print', $q->id)); ?>" 
                                           class="btn-icon" 
                                           title="พิมพ์ใบเสนอราคา">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <a href="<?php echo e(route('quotations.edit', $q)); ?>" 
                                           class="btn-icon"
                                           title="แก้ไขใบเสนอราคา">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button wire:click="delete(<?php echo e($q->id); ?>)" 
                                                class="btn-icon danger"
                                                onclick="return confirm('ยืนยันลบใบเสนอราคา <?php echo e($q->quote_number); ?>?')"
                                                title="ลบใบเสนอราคา">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="ri-file-search-line"></i>
                                        <h6>ไม่พบข้อมูลใบเสนอราคา</h6>
                                        <p>ลองค้นหาด้วยคำอื่น หรือสร้างใบเสนอราคาใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
                
                
                <!--[if BLOCK]><![endif]--><?php if($quotes->hasPages()): ?>
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="color: #6b7280; font-size: 14px;">
                            แสดง <?php echo e($quotes->firstItem()); ?>-<?php echo e($quotes->lastItem()); ?> จาก <?php echo e($quotes->total()); ?> รายการ
                        </div>
                        <div>
                            <?php echo e($quotes->links('pagination::bootstrap-5')); ?>

                        </div>
                    </div>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/quotations/quotation-index.blade.php ENDPATH**/ ?>