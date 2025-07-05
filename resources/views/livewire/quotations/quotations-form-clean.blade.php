<div>
    <style>
        /* Flowaccount-inspired clean design */
        .page-container {
            background: #f8fafc;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .page-header {
            padding: 24px 32px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .page-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        
        .form-section {
            padding: 24px 32px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn {
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .btn-outline {
            background: white;
            border-color: #d1d5db;
            color: #374151;
        }
        
        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        
        .btn-success {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        
        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .product-table th {
            background: #f9fafb;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .product-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        
        .product-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .summary-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .summary-total {
            font-weight: 600;
            font-size: 16px;
            color: #059669;
            border-top: 1px solid #d1d5db;
            padding-top: 12px;
            margin-top: 8px;
        }
        
        .customer-info {
            background: #f8fafc;
            padding: 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            min-height: 140px;
        }
        
        .customer-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .customer-detail {
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 4px;
        }
        
        .action-buttons {
            padding: 24px 32px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .action-buttons .btn {
            margin: 0 8px;
            padding: 12px 24px;
        }
    </style>

    <div class="page-container">
        <div class="content-wrapper">
            {{-- Page Header --}}
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            @if($this->isCreate) สร้างใบเสนอราคา @else แก้ไขใบเสนอราคา @endif
                        </h1>
                        <p class="page-subtitle">จัดการข้อมูลใบเสนอราคา</p>
                    </div>
                    @if (!$this->isCreate)
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light border px-3 py-2">{{ $quotation->quote_number }}</span>
                            {!! quote_status_badge($quotation->quote_status) !!}
                        </div>
                    @endif
                </div>
            </div>

            <form wire:submit.prevent="save">
                {{-- Company Info --}}
                <div class="form-section">
                    <div class="info-box">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <img src="/images/logo-crrtm.png" alt="Logo" height="50" class="me-3">
                                    <div>
                                        <h6 class="mb-1" style="color: #374151; font-weight: 600;">บริษัท เจริญมั่น คอนกรีต จำกัด</h6>
                                        <p class="mb-0 text-muted small">99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง จังหวัดนนทบุรี 11110</p>
                                        <p class="mb-0 text-muted small">โทร 082-4789197 | เลขประจำตัวผู้เสียภาษี 0125560015546</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @if (!$this->isCreate)
                                    <img src="{{ route('qr.quotation', $quotation->id) }}" alt="QR" style="height:70px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quote Date --}}
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">วันที่ออกใบเสนอราคา</label>
                                <input type="date" class="form-control"
                                       {{ $quote_status === 'success' ? 'disabled' : '' }}
                                       wire:model="quote_date">
                            </div>
                        </div>
                        <div class="form-col">
                            @if ($quotation && $quote_status === 'wait')
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-success w-100"
                                            wire:click="approveQuotation({{ $quotation->id }})"
                                            onclick="return confirm('ยืนยันการอนุมัติใบเสนอราคา เลขที่ {{ $quotation->quote_number }} ?')">
                                        <i class="ri-check-line me-2"></i>อนุมัติใบเสนอราคา
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Customer & Delivery Section --}}
                <div class="form-section">
                    <div class="section-title">ข้อมูลลูกค้าและการจัดส่ง</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight: 600; color: #374151;">ข้อมูลลูกค้า</h6>
                            
                            <div class="form-row align-items-end">
                                <div class="form-col">
                                    <div class="form-group">
                                        <label class="form-label">เลือกลูกค้า</label>
                                        <select id="customerSelect" class="form-select"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}>
                                            <option value="">-- เลือกลูกค้า --</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}" @selected($c->id == $customer_id)>
                                                    {{ $c->customer_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div style="width: auto;">
                                    <button type="button" class="btn btn-outline" 
                                            onclick="Livewire.dispatch('create-customer')"
                                            title="เพิ่มลูกค้าใหม่">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="customer-info">
                                @if ($selectedCustomer)
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="customer-name">{{ $selectedCustomer->customer_contract_name }}</div>
                                        @if ($customer_id)
                                            <button type="button" class="btn btn-sm btn-outline"
                                                    onclick="Livewire.dispatch('edit-customer', { id: {{ $customer_id }} })"
                                                    title="แก้ไขข้อมูลลูกค้า">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="customer-detail">📞 {{ $selectedCustomer->customer_phone?? 'ไม่พบข้อมูล' }}</div>
                                    <div class="customer-detail">📍 {{ $selectedCustomer->customer_address?? 'ไม่พบข้อมูล' }}</div>
                                    <div class="customer-detail">🏢 {{ $selectedCustomer->customer_taxid?? 'ไม่พบข้อมูล' }}</div>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-user-add-line fs-1 mb-2"></i>
                                        <p class="mb-0">กรุณาเลือกลูกค้า</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight: 600; color: #374151;">ที่อยู่จัดส่ง</h6>
                            <div class="form-row align-items-end">
                                <div class="form-col">
                                    <div class="form-group">
                                        <label class="form-label">เลือกที่อยู่จัดส่ง</label>
                                        <select wire:model.live="selected_delivery_id" class="form-select"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}>
                                            <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                                            @foreach ($customerDelivery as $delivery)
                                                <option value="{{ $delivery->id }}" @if ($delivery->id == $selected_delivery_id) selected @endif>
                                                    {{ $delivery->delivery_contact_name }} - {{ $delivery->delivery_phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div style="width: auto;">
                                    @if ($selectedCustomer)
                                        <button type="button" class="btn btn-outline" 
                                                wire:click.prevent="openDeliveryModal({{ $customer_id }})"
                                                title="เพิ่มที่อยู่จัดส่ง">
                                            <i class="ri-add-line"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline" disabled title="เลือกลูกค้าก่อน">
                                            <i class="ri-add-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="customer-info">
                                @if ($selectedDelivery)
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="customer-name">{{ $selectedDelivery->delivery_contact_name }}</div>
                                        <button type="button" class="btn btn-sm btn-outline"
                                                onclick="Livewire.dispatch('edit-delivery-modal', { deliveryId: {{ $selectedDelivery->id }} })"
                                                title="แก้ไขที่อยู่จัดส่ง">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </div>
                                    <div class="customer-detail">📞 {{ $selectedDelivery->delivery_phone }}</div>
                                    <div class="customer-detail">📍 {{ $selectedDelivery->delivery_address }}</div>
                                @else
                                    @if ($selectedCustomer)
                                        <div class="border border-warning border-2 p-3 rounded" style="background: #fef3c7;">
                                            <div class="customer-detail text-warning"><strong>⚠️ ใช้ที่อยู่ลูกค้า</strong></div>
                                            <div class="customer-name">{{ $selectedCustomer->customer_contract_name }}</div>
                                            <div class="customer-detail">📞 {{ $selectedCustomer->customer_phone }}</div>
                                            <div class="customer-detail">📍 {{ $selectedCustomer->customer_address }}</div>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="ri-truck-line fs-1 mb-2"></i>
                                            <p class="mb-0">เลือกลูกค้าเพื่อดูที่อยู่จัดส่ง</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Section --}}
                <div class="form-section">
                    <div class="section-title">รายการสินค้า</div>
                    
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 300px;">รายการสินค้า</th>
                                <th style="width: 150px;">รายละเอียด</th>
                                <th style="width: 80px;">VAT</th>
                                <th style="width: 100px;">ความยาว</th>
                                <th style="width: 100px;">จำนวน</th>
                                <th style="width: 80px;">หน่วย</th>
                                <th style="width: 120px;">ราคา/หน่วย</th>
                                <th style="width: 120px;">รวม</th>
                                <th style="width: 60px;">ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $i => $item)
                                <tr wire:key="row-{{ $i }}">
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="position-relative" wire:ignore.self>
                                            <input type="text" class="form-control mb-2"
                                                {{ $quote_status === 'success' ? 'disabled' : '' }}
                                                placeholder="ค้นหาสินค้า..."
                                                wire:model.live.debounce.500ms="items.{{ $i }}.product_search"
                                                wire:keydown.escape="$set('items.{{ $i }}.product_results', [])"
                                                wire:focus="$set('items.{{ $i }}.product_results_visible', true)"
                                                wire:key="search-{{ $i }}" />
                                            
                                            <input type="text" class="form-control"
                                                wire:model="items.{{ $i }}.product_note"
                                                placeholder="หมายเหตุ">

                                            @if (!empty($item['product_results_visible']))
                                                <div class="position-absolute w-100 mt-1" style="z-index: 1000;">
                                                    <div class="list-group shadow rounded" style="max-height: 300px; overflow-y: auto;">
                                                        @foreach ($item['product_results'] as $result)
                                                            <a href="javascript: void(0);" class="list-group-item list-group-item-action"
                                                                wire:click="selectProduct({{ $i }}, {{ $result->product_id }}, @js($result->product_name))">
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <h6 class="mb-1">{{ $result->product_name }}</h6>
                                                                        <small class="text-muted">{{ $result->product_size }} | {{ $result->productWireType?->value ?? '-' }}</small>
                                                                    </div>
                                                                    <i class="ri-arrow-right-s-line"></i>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item['product_calculation'] != 1)
                                            <input type="number" step="0.01" class="form-control"
                                                wire:model.debounce.300ms="items.{{ $i }}.product_calculation"
                                                {{ $quote_status === 'success' ? 'disabled' : '' }}
                                                placeholder="ความหนา" />
                                        @else
                                            <div class="text-muted small">
                                                {!! $item['product_detail'] ?? 'ไม่มีรายละเอียด' !!}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model.live="items.{{ $i }}.product_vat"
                                                wire:change="refreshVat">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}
                                            wire:model.live.debounce.300ms="items.{{ $i }}.product_length">
                                    </td>
                                    <td>
                                        <input type="number" min="1" class="form-control"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}
                                            wire:model.live.debounce.300ms="items.{{ $i }}.quantity" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}
                                            wire:model.live="items.{{ $i }}.product_unit"
                                            readonly style="background: #f3f4f6;">
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="0.01" class="form-control"
                                            {{ $quote_status === 'success' ? 'disabled' : '' }}
                                            wire:model.live.debounce.300ms="items.{{ $i }}.unit_price" />
                                    </td>
                                    <td class="text-end">
                                        <strong style="color: #059669;">{{ number_format($item['total'], 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if (!$quotation?->quote_status == 'success')
                                            <button type="button" class="btn btn-sm" style="color: #ef4444;"
                                                wire:click="removeItem({{ $i }})"
                                                title="ลบรายการ">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary"
                            wire:click="addEmptyItem">
                            <i class="ri-add-line me-2"></i>เพิ่มรายการสินค้า
                        </button>
                    </div>
                </div>

                {{-- Summary Section --}}
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-title">หมายเหตุและข้อมูลเพิ่มเติม</div>
                            
                            <div class="form-group">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                        wire:model.live="quote_enable_vat"
                                        {{ $quote_status === 'success' ? 'disabled' : '' }} 
                                        id="enableVatCheck">
                                    <label class="form-check-label" for="enableVatCheck">
                                        คำนวณ VAT 7%
                                    </label>
                                </div>
                                
                                <label class="form-label">หมายเหตุ</label>
                                <textarea wire:model="quote_note" 
                                    class="form-control" 
                                    rows="4" 
                                    {{ $quote_status === 'success' ? 'disabled' : '' }}
                                    placeholder="หมายเหตุเพิ่มเติม"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-title">สรุปยอดเงิน</div>
                            
                            <div class="summary-box">
                                <div class="summary-row">
                                    <span>จำนวนเงินรวม:</span>
                                    <span>{{ number_format($quote_subtotal, 2) }} บาท</span>
                                </div>
                                
                                <div class="summary-row">
                                    <span>ส่วนลด:</span>
                                    <div style="width: 120px;">
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                wire:model.live.debounce.300ms="quote_discount"
                                                {{ $quote_status === 'success' ? 'disabled' : '' }}
                                                class="form-control text-end" 
                                                min="0" 
                                                step="0.01">
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="summary-row">
                                    <span>ภาษีมูลค่าเพิ่ม (7%):</span>
                                    <span>{{ number_format($quote_vat, 2) }} บาท</span>
                                </div>
                                
                                <div class="summary-row summary-total">
                                    <span>จำนวนเงินทั้งสิ้น:</span>
                                    <span>{{ number_format($quote_grand_total, 2) }} บาท</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Action Buttons --}}
            <div class="action-buttons">
                @if (!$this->isCreate)
                    <a href="{{ route('quotations.print', $quotation_id) }}" 
                       class="btn btn-outline">
                        <i class="ri-printer-line me-2"></i>พิมพ์ใบเสนอราคา
                    </a>
                @endif

                @if (!$this->isCreate)
                    <button type="submit" 
                            class="btn btn-primary"
                            {{ $quote_status === 'success' ? 'disabled' : '' }}
                            form="quotation-form">
                        <i class="ri-save-line me-2"></i>บันทึกการแก้ไข
                    </button>
                @else
                    <button type="submit" class="btn btn-primary" form="quotation-form">
                        <i class="ri-add-circle-line me-2"></i>สร้างใบเสนอราคา
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <livewire:quotations.customer-modal />
    <livewire:quotations.delivery-address-modal />

    {{-- Scripts --}}
    <script>
        document.addEventListener('open-delivery-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('bs-example-modal-lg'));
            modal.show();
        });
        
        document.addEventListener('close-delivery-modal', () => {
            const modalEl = document.getElementById('bs-example-modal-lg');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style = '';
            }, 300);
        });

        document.addEventListener('open-customer-modal', () => {
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        });
        
        document.addEventListener('close-customer-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
            if (modal) modal.hide();
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
    </script>
</div>
