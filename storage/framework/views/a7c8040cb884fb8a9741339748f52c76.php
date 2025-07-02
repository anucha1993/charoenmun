<div>
    <style>
        /* Clean & Modern Design - Flowaccount Inspired */
        .page-container {
            background: #f7f9fc;
            min-height: 100vh;
            padding: 16px;
        }
        
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: visible;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px 40px;
            position: relative;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .page-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }
        
        .status-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }
        
        .form-section {
            padding: 20px 32px;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
            overflow: visible;
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .company-info {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 16px;
        }
        
        .form-grid-date {
            display: grid;
            grid-template-columns: 280px 1fr auto;
            gap: 16px;
            align-items: end;
            margin-bottom: 16px;
        }
        
        .customer-section {
            background: #fafbfc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }
        
        .customer-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .customer-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .form-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 14px;
            transition: all 0.3s ease;
        }
        
        .form-card:hover {
            border-color: #d1d5db;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .form-group {
            margin-bottom: 14px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .input-group-modern {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        
        .input-group-modern .form-control,
        .input-group-modern .form-select {
            flex: 1;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .btn {
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline {
            background: white;
            border-color: #e5e7eb;
            color: #6b7280;
        }
        
        .btn-outline:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-1px);
        }
        
        .btn-outline-danger {
            background: white;
            border-color: #fecaca;
            color: #ef4444;
        }
        
        .btn-outline-danger:hover {
            background: #fef2f2;
            border-color: #f87171;
            color: #dc2626;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: transparent;
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 6px;
        }
        
        .product-section {
            background: #fafbfc;
            border-radius: 8px;
            padding: 16px;
            margin-top: 6px;
            position: relative;
            overflow: visible;
        }
        
        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 8px;
            overflow: visible;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        .product-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 12px 8px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .product-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        
        .product-table tbody tr {
            transition: all 0.2s ease;
            position: relative;
        }
        
        .product-table tbody tr:hover {
            background: #f8fafc;
            z-index: 1;
        }
        
        .product-table .form-control {
            padding: 8px 10px;
            font-size: 14px;
            border: 1px solid #e5e7eb;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }
        
        .notes-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
        }
        
        .summary-box {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 15px;
            font-weight: 500;
        }
        
        .summary-total {
            font-weight: 700;
            font-size: 17px;
            color: #059669;
            border-top: 2px solid #d1d5db;
            padding-top: 12px;
            margin-top: 8px;
        }
        
        .customer-info {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            min-height: 100px;
            transition: all 0.3s ease;
        }
        
        .customer-info:hover {
            border-color: #d1d5db;
        }
        
        .customer-name {
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .customer-detail {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 4px;
        }
        
        .action-buttons {
            padding: 20px 32px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-top: 2px solid #e2e8f0;
            text-align: center;
        }
        
        .action-buttons .btn {
            margin: 0 8px;
            padding: 12px 24px;
            font-size: 14px;
        }
        
        .warning-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 6px;
            padding: 12px;
        }
        
        .empty-state {
            text-align: center;
            padding: 24px 16px;
            color: #9ca3af;
        }
        
        .empty-state i {
            font-size: 32px;
            margin-bottom: 8px;
            opacity: 0.5;
        }
        
        /* Product Search Dropdown */
        .product-search-container {
            position: relative;
            z-index: 1;
        }
        
        .product-search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 9999;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-height: 300px;
            overflow-y: auto;
            margin-top: 4px;
            animation: fadeIn 0.2s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .product-search-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: all 0.2s ease;
            display: block;
            text-decoration: none;
            color: inherit;
        }
        
        .product-search-item:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: inherit;
            text-decoration: none;
            transform: translateX(2px);
        }
        
        .product-search-item:last-child {
            border-bottom: none;
        }
        
        .product-search-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .product-search-detail {
            font-size: 12px;
            color: #64748b;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .form-grid,
            .form-grid-date,
            .customer-row,
            .summary-section {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .form-grid-date {
                grid-template-columns: 1fr;
            }
            
            .page-container {
                padding: 8px;
            }
            
            .form-section {
                padding: 16px 20px;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .action-buttons {
                padding: 20px;
            }
            
            .product-table {
                font-size: 12px;
            }
            
            .product-table th,
            .product-table td {
                padding: 8px 4px;
            }
            
            .section-title {
                font-size: 18px;
            }
            
            .customer-name {
                font-size: 15px;
            }
            
            .customer-detail {
                font-size: 13px;
            }
        }
    </style>

    <div class="page-container">
        <div class="content-wrapper">
            
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <!--[if BLOCK]><![endif]--><?php if($this->isCreate): ?> 
                                <i class="ri-add-circle-line me-3"></i>สร้างใบเสนอราคา 
                            <?php else: ?> 
                                <i class="ri-edit-line me-3"></i>แก้ไขใบเสนอราคา 
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h1>
                        <p class="page-subtitle">จัดการข้อมูลใบเสนอราคา รวดเร็วและแม่นยำ</p>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="status-badge"><?php echo e($quotation->quote_number); ?></div>
                            <?php echo quote_status_badge($quotation->quote_status); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <form wire:submit.prevent="save" id="quotation-form">
                
                <div class="form-section">
                    <div class="company-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <img src="/images/logo-crrtm.png" alt="Logo" height="60" class="me-4">
                                    <div>
                                        <h5 class="mb-2" style="color: #1e293b; font-weight: 700;">บริษัท เจริญมั่น คอนกรีต จำกัด</h5>
                                        <p class="mb-1 text-muted">99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง จังหวัดนนทบุรี 11110</p>
                                        <p class="mb-0 text-muted">📞 082-4789197 | 🆔 เลขประจำตัวผู้เสียภาษี 0125560015546</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                                    <img src="<?php echo e(route('qr.quotation', $quotation->id)); ?>" alt="QR Code" style="height:80px; border-radius: 8px;">
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>

                    
                    <div class="form-grid-date">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-calendar-line me-2"></i>วันที่ออกใบเสนอราคา
                            </label>
                            <input type="date" class="form-control"
                                   <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                   wire:model="quote_date">
                        </div>
                        <div></div>
                        <!--[if BLOCK]><![endif]--><?php if($quotation && $quote_status === 'wait'): ?>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success"
                                        wire:click="approveQuotation(<?php echo e($quotation->id); ?>)"
                                        onclick="return confirm('ยืนยันการอนุมัติใบเสนอราคา เลขที่ <?php echo e($quotation->quote_number); ?> ?')">
                                    <i class="ri-check-line me-2"></i>อนุมัติใบเสนอราคา
                                </button>
                                <button type="button" class="btn btn-outline-danger"
                                        wire:click="rejectQuotation(<?php echo e($quotation->id); ?>)"
                                        onclick="return confirm('ยืนยันการไม่อนุมัติใบเสนอราคา เลขที่ <?php echo e($quotation->quote_number); ?> ?')">
                                    <i class="ri-close-line me-2"></i>ไม่อนุมัติใบเสนอราคา
                                </button>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div class="form-section">
                    <div class="section-title">
                        <i class="ri-user-heart-line"></i>ข้อมูลลูกค้าและการจัดส่ง
                    </div>
                    
                    <div class="customer-section">
                        <div class="customer-row">
                            
                            <div class="customer-column">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <i class="ri-user-line" style="color: #667eea;"></i>
                                    <h2 class="mb-0" style="font-weight: 600; color: #374151; font-size: 18px;">ข้อมูลลูกค้า</h2>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">เลือกลูกค้า</label>
                                    <div class="input-group-modern">
                                        <select id="customerSelect" class="form-select"
                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>>
                                            <option value="">-- เลือกลูกค้า --</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($c->id); ?>" <?php if($c->id == $customer_id): echo 'selected'; endif; ?>>
                                                    <?php echo e($c->customer_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <button type="button" class="btn btn-outline btn-icon" 
                                                onclick="Livewire.dispatch('create-customer')"
                                                title="เพิ่มลูกค้าใหม่">
                                            <i class="ri-add-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="customer-info">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                        <div class="d-flex justify-content-between align-items-start mb-0">
                                            <div class="customer-name"><?php echo e($selectedCustomer->customer_contract_name); ?></div>
                                            <!--[if BLOCK]><![endif]--><?php if($customer_id): ?>
                                                <button type="button" class="btn btn-sm btn-outline"
                                                        onclick="Livewire.dispatch('edit-customer', { id: <?php echo e($customer_id); ?> })"
                                                        title="แก้ไขข้อมูลลูกค้า">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="customer-detail">📞 <?php echo e($selectedCustomer->customer_phone); ?></div>
                                        <div class="customer-detail">📍 <?php echo e($selectedCustomer->customer_address); ?>

                                            <?php echo e($selectedCustomer->customer_district_name); ?>

                                            <?php echo e($selectedCustomer->customer_amphur_name); ?>

                                            <?php echo e($selectedCustomer->customer_province_name); ?>

                                            <?php echo e($selectedCustomer->customer_zipcode); ?></div>
                                        <div class="customer-detail">🏢 <?php echo e($selectedCustomer->customer_taxid); ?></div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="ri-user-add-line"></i>
                                            <p class="mb-0">กรุณาเลือกลูกค้า</p>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            
                            <div class="customer-column">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <i class="ri-truck-line" style="color: #667eea;"></i>
                                    <h6 class="mb-0" style="font-weight: 600; color: #374151; font-size: 18px;">ที่อยู่จัดส่ง</h6>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">เลือกที่อยู่จัดส่ง</label>
                                    <div class="input-group-modern">
                                        <select wire:model.live="selected_delivery_id" class="form-select"
                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>>
                                            <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($delivery->id); ?>" <?php if($delivery->id == $selected_delivery_id): ?> selected <?php endif; ?>>
                                                    <?php echo e($delivery->delivery_contact_name); ?> - <?php echo e($delivery->delivery_phone); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                            <button type="button" class="btn btn-outline btn-icon" 
                                                    wire:click.prevent="openDeliveryModal(<?php echo e($customer_id); ?>)"
                                                    title="เพิ่มที่อยู่จัดส่ง">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline btn-icon" disabled title="เลือกลูกค้าก่อน">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <div class="customer-info">
                                    <!--[if BLOCK]><![endif]--><?php if($selectedDelivery): ?>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="customer-name"><?php echo e($selectedDelivery->delivery_contact_name); ?></div>
                                            <button type="button" class="btn btn-sm btn-outline"
                                                    onclick="Livewire.dispatch('edit-delivery-modal', { deliveryId: <?php echo e($selectedDelivery->id); ?> })"
                                                    title="แก้ไขที่อยู่จัดส่ง">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        </div>
                                        <div class="customer-detail">📞 <?php echo e($selectedDelivery->delivery_phone); ?></div>
                                        <div class="customer-detail">📍 <?php echo e($selectedDelivery->delivery_address); ?></div>
                                    <?php else: ?>
                                        <!--[if BLOCK]><![endif]--><?php if($selectedCustomer): ?>
                                            <div class="warning-box">
                                                <div class="customer-detail text-warning"><strong>⚠️ ใช้ที่อยู่ลูกค้า</strong></div>
                                                <div class="customer-name"><?php echo e($selectedCustomer->customer_contract_name); ?></div>
                                                <div class="customer-detail">📞 <?php echo e($selectedCustomer->customer_phone); ?></div>
                                                <div class="customer-detail">📍 <?php echo e($selectedCustomer->customer_address); ?>

                                                    <?php echo e($selectedCustomer->customer_district_name); ?>

                                                    <?php echo e($selectedCustomer->customer_amphur_name); ?>

                                                    <?php echo e($selectedCustomer->customer_province_name); ?>

                                                    <?php echo e($selectedCustomer->customer_zipcode); ?></div>
                                            </div>
                                        <?php else: ?>
                                            <div class="empty-state">
                                                <i class="ri-truck-line"></i>
                                                <p class="mb-0">เลือกลูกค้าเพื่อดูที่อยู่จัดส่ง</p>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="form-section">
                    <div class="section-title">
                        <i class="ri-shopping-cart-line"></i>รายการสินค้า
                    </div>
                    
                    <div class="product-section">
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th style="width: 280px;">รายการสินค้า</th>
                                    <th style="width: 120px;">รายละเอียด</th>
                                    <th style="width: 60px;">VAT</th>
                                    <th style="width: 90px;">ความยาว</th>
                                    <th style="width: 80px;">จำนวน</th>
                                    <th style="width: 70px;">หน่วย</th>
                                    <th style="width: 100px;">ราคา/หน่วย</th>
                                    <th style="width: 100px;">รวม</th>
                                    <th style="width: 50px;">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr wire:key="row-<?php echo e($i); ?>">
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark"><?php echo e($i + 1); ?></span>
                                        </td>
                                        <td>
                                            <div class="product-search-container">
                                                <div class="d-flex gap-2 mb-2">
                                                    <input type="text" class="form-control <?php $__errorArgs = ['items.' . $i . '.product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php $__errorArgs = ['items.' . $i . '.product_search'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                        placeholder="🔍 ค้นหาสินค้า..."
                                                        wire:model.live.debounce.500ms="items.<?php echo e($i); ?>.product_search"
                                                        wire:keydown.escape="$set('items.<?php echo e($i); ?>.product_results_visible', false)"
                                                        wire:focus="$set('items.<?php echo e($i); ?>.product_results_visible', true)"
                                                        wire:key="search-<?php echo e($i); ?>" />
                                                    
                                                    <!--[if BLOCK]><![endif]--><?php if(!empty($item['product_id'])): ?>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                            wire:click="clearProductSelection(<?php echo e($i); ?>)"
                                                            title="เคลียร์การเลือกสินค้า"
                                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>>
                                                            <i class="ri-close-line"></i>
                                                        </button>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['items.' . $i . '.product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback d-block">
                                                        <small class="text-danger"><?php echo e($message); ?></small>
                                                    </div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['items.' . $i . '.product_search'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback d-block">
                                                        <small class="text-danger"><?php echo e($message); ?></small>
                                                    </div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <input type="text" class="form-control"
                                                    wire:model="items.<?php echo e($i); ?>.product_note"
                                                    placeholder="💬 หมายเหตุ">

                                                <!--[if BLOCK]><![endif]--><?php if(!empty($item['product_results_visible']) && !empty($item['product_results'])): ?>
                                                    <div class="product-search-dropdown">
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $item['product_results']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="product-search-item"
                                                                wire:click="selectProduct(<?php echo e($i); ?>, <?php echo e($result->product_id); ?>, <?php echo \Illuminate\Support\Js::from($result->product_name)->toHtml() ?>)">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <div class="product-search-title"><?php echo e($result->product_name); ?></div>
                                                                        <div class="product-search-detail">
                                                                            <?php echo e($result->product_size); ?> | <?php echo e($result->productWireType?->value ?? '-'); ?>

                                                                        </div>
                                                                    </div>
                                                                    <i class="ri-arrow-right-s-line" style="color: #9ca3af;"></i>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </td>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if($item['product_calculation'] != 1): ?>
                                                <input type="number" step="0.01" class="form-control text-center"
                                                    wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.product_calculation"
                                                    <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                    placeholder="ความหนา/จำนวนที่ใช้คำนวณ" />
                                            <?php else: ?>
                                                <div class="text-muted small text-center">
                                                    <?php echo $item['product_detail'] ?? '-'; ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="items.<?php echo e($i); ?>.product_vat"
                                                    wire:change="refreshVat">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.product_length"
                                                placeholder="ยาว">
                                        </td>
                                        <td>
                                            <input type="number" min="1" class="form-control text-center"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.quantity"
                                                placeholder="จำนวน" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                wire:model.live="items.<?php echo e($i); ?>.product_unit"
                                                readonly style="background: #f8fafc; font-size: 12px;">
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.01" class="form-control text-end"
                                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                                wire:model.live.debounce.300ms="items.<?php echo e($i); ?>.unit_price"
                                                placeholder="0.00" />
                                        </td>
                                        <td class="text-end">
                                            <strong style="color: #059669; font-size: 15px;">
                                                ฿<?php echo e(number_format(($item['unit_price'] ?? 0) * ($item['product_calculation'] ?? 1) * ($item['quantity'] ?? 0), 2)); ?>

                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <!--[if BLOCK]><![endif]--><?php if(!$quotation?->quote_status == 'success'): ?>
                                                <button type="button" class="btn btn-sm btn-outline" 
                                                    style="color: #ef4444; border-color: #fecaca;"
                                                    wire:click="removeItem(<?php echo e($i); ?>)"
                                                    title="ลบรายการ">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary"
                                wire:click="addEmptyItem">
                                <i class="ri-add-line me-2"></i>เพิ่มรายการสินค้า
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="form-section">
                    <div class="section-title">
                        <i class="ri-calculator-line"></i>สรุปยอดเงินและหมายเหตุ
                    </div>
                    
                    <div class="summary-section">
                        <div class="notes-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri-sticky-note-line" style="color: #667eea;"></i>
                                <h6 class="mb-0" style="font-weight: 600; color: #374151; font-size: 15px;">หมายเหตุและข้อมูลเพิ่มเติม</h6>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check mb-2" style="padding: 8px; background: #f8fafc; border-radius: 6px;">
                                    <input class="form-check-input" type="checkbox" 
                                        wire:model.live="quote_enable_vat"
                                        <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?> 
                                        id="enableVatCheck">
                                    <label class="form-check-label" for="enableVatCheck" style="font-weight: 600; font-size: 14px;">
                                        <i class="ri-percent-line me-2"></i>คำนวณ VAT 7%
                                    </label>
                                </div>
                                
                                <label class="form-label">
                                    <i class="ri-file-text-line me-2"></i>หมายเหตุ
                                </label>
                                <textarea wire:model="quote_note" 
                                    class="form-control" 
                                    rows="4" 
                                    <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                    placeholder="💬 หมายเหตุเพิ่มเติม เช่น เงื่อนไขการชำระเงิน, การจัดส่ง, การรับประกัน..."
                                    style="resize: vertical; font-size: 14px;"></textarea>
                            </div>
                        </div>

                        <div class="summary-box">
                            <div class="text-center mb-2">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <i class="ri-money-dollar-circle-line" style="color: #667eea;"></i>
                                    <h6 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 15px;">สรุปยอดเงิน</h6>
                                </div>
                            </div>
                            <div class="summary-row">
                                <span><i class="ri-shopping-bag-line me-2"></i>ยอดรวมก่อนหักส่วนลด:</span>
                                <span style="font-weight: 600; color: #888;">฿<?php echo e(number_format($quote_subtotal_before_discount, 2)); ?></span>
                            </div>
                            <div class="summary-row">
                                <span><i class="ri-coupon-line me-2"></i>ส่วนลด:</span>
                                <div style="width: 130px;">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            wire:model.live.debounce.300ms="quote_discount"
                                            <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                            class="form-control text-end"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                            style="font-size: 13px; padding: 6px 8px;">
                                        <span class="input-group-text" style="font-size: 13px;">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="summary-row">
                                <span><i class="ri-shopping-bag-line me-2"></i>ยอดสุทธิหลังหักส่วนลด:</span>
                                <span style="font-weight: 600; color: #059669;">฿<?php echo e(number_format($quote_subtotal_before_discount - $quote_discount, 2)); ?></span>
                            </div>
                            <div class="summary-row">
                                <span><i class="ri-percent-line me-2"></i>ภาษีมูลค่าเพิ่ม (7%):</span>
                                <span style="font-weight: 600;">฿<?php echo e(number_format($quote_vat, 2)); ?></span>
                            </div>
                            <div class="summary-row summary-total">
                                <span><i class="ri-money-dollar-box-line me-2"></i>จำนวนเงินทั้งสิ้น:</span>
                                <span>฿<?php echo e(number_format(($quote_subtotal_before_discount - $quote_discount) + $quote_vat, 2)); ?></span>
                            </div>

                           
                        </div>
                    </div>
                </div>
            </form>

            
            <div class="action-buttons">
                <div class="d-flex justify-content-center gap-3">
                    <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                        <a href="<?php echo e(route('quotations.print', $quotation_id)); ?>" 
                           class="btn btn-outline">
                            <i class="ri-printer-line me-2"></i>พิมพ์ใบเสนอราคา
                        </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if(!$this->isCreate): ?>
                        <button type="submit" 
                                class="btn btn-primary"
                                <?php echo e($quote_status === 'success' ? 'disabled' : ''); ?>

                                form="quotation-form">
                            <i class="ri-save-line me-2"></i>บันทึกการแก้ไข
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" form="quotation-form">
                            <i class="ri-add-circle-line me-2"></i>สร้างใบเสนอราคา
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
        
        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('quotations.customer-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1200077831-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('quotations.delivery-address-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1200077831-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        
        <script>
        // ✅ Helper function สำหรับทำความสะอาด modal อย่างสมบูรณ์
        function cleanupModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;

            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }

            // ทำความสะอาดอย่างสมบูรณ์หลังปิด modal
            setTimeout(() => {
                // ลบ backdrop ทั้งหมด
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                
                // รีเซ็ต body styles และ classes
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('margin-right');
                
                // รีเซ็ต modal classes
                modalEl.classList.remove('show');
                modalEl.style.removeProperty('display');
                modalEl.style.removeProperty('padding-right');
                modalEl.setAttribute('aria-hidden', 'true');
                modalEl.removeAttribute('aria-modal');
                
                // บังคับให้หน้าเลื่อนได้
                document.documentElement.style.removeProperty('overflow');
                document.documentElement.style.removeProperty('padding-right');
                
                // เคลียร์ style attribute ทั้งหมดถ้าเป็นค่าเริ่มต้น
                if (document.body.style.length === 0) {
                    document.body.removeAttribute('style');
                }
                if (document.documentElement.style.length === 0) {
                    document.documentElement.removeAttribute('style');
                }
            }, 350); // เพิ่มเวลาให้มากขึ้นเล็กน้อย
        }

        document.addEventListener('open-delivery-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('bs-example-modal-lg'));
            modal.show();
        });
        
        document.addEventListener('close-delivery-modal', () => {
            cleanupModal('bs-example-modal-lg');
        });

        // เพิ่มการจัดการเมื่อสร้างที่อยู่จัดส่งสำเร็จ
        document.addEventListener('delivery-address-created', () => {
            cleanupModal('bs-example-modal-lg');
        });

        // เพิ่มการจัดการเมื่อแก้ไขที่อยู่จัดส่งสำเร็จ
        document.addEventListener('delivery-address-updated', () => {
            cleanupModal('bs-example-modal-lg');
        });

        document.addEventListener('open-customer-modal', () => {
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        });
        
        document.addEventListener('close-customer-modal', () => {
            cleanupModal('customerModal');
        });

        // เพิ่มการจัดการเมื่อสร้างลูกค้าสำเร็จ
        document.addEventListener('customer-created', () => {
            cleanupModal('customerModal');
        });

        // เพิ่มการจัดการเมื่อแก้ไขลูกค้าสำเร็จ
        document.addEventListener('customer-updated', () => {
            cleanupModal('customerModal');
        });

        document.addEventListener('DOMContentLoaded', function() {
            let select = $('#customerSelect');
            select.select2();
            select.on('change', function() {
                let selectedId = $(this).val();
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                livewireComponent.call('setCustomerId', selectedId);
            });
        });

        document.addEventListener('customer-created-success', function(e) {
            const detail = e.detail?.[0] ?? {};
            const customerId = parseInt(detail.customerId);
            const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
            
            livewireComponent.call('refreshCustomers').then(() => {
                setTimeout(() => {
                    const found = $(`#customerSelect option[value="${customerId}"]`).length > 0;
                    if (found) {
                        $('#customerSelect').val(customerId).trigger('change');
                        livewireComponent.call('setCustomerId', customerId);
                    }
                }, 300);
            });
        });

        document.addEventListener('delivery-created-success', function(e) {
            const detail = e.detail?.[0] ?? {};
            const deliveryId = parseInt(detail.deliveryId);
            const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
            
            setTimeout(() => {
                const $dropdown = $("select[name='selected_delivery_id']");
                const found = $dropdown.find(`option[value='${deliveryId}']`).length > 0;
                if (found) {
                    $dropdown.val(deliveryId).trigger('change');
                }
            }, 500);
        });

        // Handle product search dropdown
        document.addEventListener('click', function(e) {
            // Hide all product search dropdowns when clicking outside
            if (!e.target.closest('.product-search-container')) {
                document.querySelectorAll('.product-search-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
                
                // Also update Livewire state
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                if (livewireComponent) {
                    // Find all visible dropdowns and hide them in Livewire state
                    const visibleDropdowns = document.querySelectorAll('.product-search-dropdown[style*="display: block"], .product-search-dropdown:not([style*="display: none"])');
                    visibleDropdowns.forEach((dropdown, index) => {
                        // Get the row index from the dropdown's parent
                        const row = dropdown.closest('tr');
                        const rowIndex = Array.from(row.parentNode.children).indexOf(row);
                        if (livewireComponent.items && livewireComponent.items[rowIndex]) {
                            livewireComponent.call('$set', `items.${rowIndex}.product_results_visible`, false);
                        }
                    });
                }
            }
        });

        // Handle escape key to close dropdowns
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.product-search-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
                
                // Also update Livewire state
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                if (livewireComponent && livewireComponent.items) {
                    livewireComponent.items.forEach((item, index) => {
                        if (item.product_results_visible) {
                            livewireComponent.call('$set', `items.${index}.product_results_visible`, false);
                        }
                    });
                }
            }
        });

        // ✅ ป้องกันการพิมพ์สินค้าที่ไม่มีในระบบ - อนุญาตให้ค้นหาได้ปกติ
        document.addEventListener('input', function(e) {
            if (e.target.matches('[wire\\:model*="product_search"]')) {
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                if (livewireComponent) {
                    // ดึงหมายเลขแถวจาก wire:model
                    const match = e.target.getAttribute('wire:model').match(/items\.(\d+)\.product_search/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        const item = livewireComponent.items[rowIndex];
                        
                        // เคลียร์ product_id เมื่อเริ่มพิมพ์ใหม่ (เพื่อให้ต้องเลือกจาก dropdown ใหม่)
                        if (e.target.value && item.product_id && !item.selected_from_dropdown) {
                            livewireComponent.call('$set', `items.${rowIndex}.product_id`, null);
                        }
                    }
                }
            }
        });

        // ✅ ป้องกันการ paste ข้อมูลเอง - อนุญาตให้ paste เพื่อค้นหาได้
        document.addEventListener('paste', function(e) {
            if (e.target.matches('[wire\\:model*="product_search"]')) {
                // อนุญาตให้ paste แต่เคลียร์ product_id เพื่อให้ต้องเลือกจาก dropdown ใหม่
                const livewireComponent = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                if (livewireComponent) {
                    const match = e.target.getAttribute('wire:model').match(/items\.(\d+)\.product_search/);
                    if (match) {
                        const rowIndex = parseInt(match[1]);
                        setTimeout(() => {
                            livewireComponent.call('$set', `items.${rowIndex}.product_id`, null);
                            livewireComponent.call('$set', `items.${rowIndex}.selected_from_dropdown`, false);
                        }, 100);
                    }
                }
            }
        });

        // Ensure dropdowns are visible when search results are available
        document.addEventListener('livewire:morph-updated', function() {
            document.querySelectorAll('.product-search-dropdown').forEach(dropdown => {
                dropdown.style.display = 'block';
            });
        });
    </script>
</div><?php /**PATH C:\laragon\www\charoenmun\resources\views/livewire/quotations/quotations-form.blade.php ENDPATH**/ ?>