{{-- resources/views/livewire/quotations/quotation-index.blade.php --}}
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
            font-size: 28px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .page-subtitle {
            font-size: 16px;
            color: #ef4444;
            margin: 4px 0 0 0;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .stat-content h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            color: #111827;
        }
        
        .stat-content p {
            font-size: 14px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-icon.primary { background: #dbeafe; color: #3b82f6; }
        .stat-icon.warning { background: #fef3c7; color: #f59e0b; }
        .stat-icon.success { background: #d1fae5; color: #10b981; }
        .stat-icon.danger { background: #fee2e2; color: #ef4444; }
        
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
            background: #f9fafb;
            padding: 16px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .clean-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
        }
        
        .clean-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .quote-number {
            font-weight: 600;
            color: #3b82f6;
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
        }
        
        .customer-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .customer-phone {
            font-size: 13px;
            color: #6b7280;
        }
        
        .amount {
            font-weight: 600;
            color: #059669;
            font-size: 16px;
        }
        
        .seller-info {
            display: flex;
            align-items: center;
        }
        
        .seller-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 8px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-icon:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f8fafc;
        }
        
        .btn-icon.danger:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
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
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
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
    </style>

    <div class="page-container">
        <div class="content-wrapper">
            {{-- Page Header --}}
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">ใบเสนอราคาไม่อนุมัติ</h1>
                        <p class="page-subtitle"><i class="ri-close-circle-line me-1"></i>รายการใบเสนอราคาที่ถูกยกเลิก</p>
                    </div>
                    <a href="{{route('quotations.create')}}" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>สร้างใบเสนอราคา
                    </a>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <h3>{{ $quotes->total() }}</h3>
                        <p>ไม่อนุมัติ</p>
                    </div>
                    <div class="stat-icon danger">
                        <i class="ri-close-circle-line"></i>
                    </div>
                </div>
            </div>

            {{-- Search and Filter --}}
            <div class="filter-card">
                <div class="filter-row">
                    <div class="filter-col">
                        <label class="form-label">ค้นหา</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="ค้นหาเลขที่ใบเสนอราคา, ชื่อลูกค้า..."
                            wire:model.debounce.500ms.live="search"
                        >
                    </div>
                    <div class="filter-col" style="flex: 0 0 200px;">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" wire:model.live="status">
                            <option value="">ทั้งหมด</option>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}">
                                    @if ($s === 'wait') รออนุมัติ
                                    @elseif ($s === 'success') อนุมัติแล้ว
                                    @else ไม่อนุมัติ
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Data Table --}}
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
                            <th style="text-align: right;">จำนวนเงิน</th>
                            <th>ผู้ขาย</th>
                            <th style="text-align: center;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotes as $q)
                            <tr>
                                <td>
                                    <span class="quote-number">{{ $q->quote_number }}</span>
                                </td>
                                <td>
                                    {!! quote_status_badge($q->quote_status) !!}
                                </td>
                                <td>
                                    {{ $q->quote_date->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">{{ $q->customer->customer_name }}</div>
                                        <div class="customer-phone">{{ $q->customer->customer_phone }}</div>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <span class="amount">฿{{ number_format($q->quote_grand_total, 2) }}</span>
                                </td>
                                <td>
                                    <div class="seller-info">
                                        <div class="seller-avatar">{{ substr($q->sale->name, 0, 1) }}</div>
                                        <span>{{ $q->sale->name }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="{{ route('quotations.print', $q->id) }}" 
                                           class="btn-icon" 
                                           title="พิมพ์">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <a href="{{ route('quotations.edit', $q) }}" 
                                           class="btn-icon"
                                           title="แก้ไข">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button wire:click="delete({{ $q->id }})" 
                                                class="btn-icon danger"
                                                onclick="return confirm('ยืนยันลบใบเสนอราคานี้?')"
                                                title="ลบ">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="ri-file-search-line"></i>
                                        <h6>ไม่พบข้อมูลใบเสนอราคา</h6>
                                        <p>ลองค้นหาด้วยคำอื่น หรือสร้างใบเสนอราคาใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{-- Pagination --}}
                @if($quotes->hasPages())
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="color: #6b7280; font-size: 14px;">
                            แสดง {{ $quotes->firstItem() }}-{{ $quotes->lastItem() }} จาก {{ $quotes->total() }} รายการ
                        </div>
                        <div>
                            {{ $quotes->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
