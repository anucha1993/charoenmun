<div>
    <style>
        .report-container {
            background: #f8fafc;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .report-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .report-header {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 28px 32px;
            margin-bottom: 24px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .report-subtitle {
            font-size: 15px;
            color: #6b7280;
            margin: 8px 0 0 0;
        }
        
        .filter-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: 200px 200px 1fr auto;
            gap: 16px;
            align-items: end;
        }
        
        /* Select2 Custom Styles */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            min-height: 42px !important;
            padding: 4px 8px !important;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            margin: 2px !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 6px !important;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .summary-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .summary-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .summary-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .summary-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .summary-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .summary-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
        .summary-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
        
        .summary-content h3 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        
        .summary-content p {
            font-size: 14px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        
        .data-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background: #f8fafc;
        }
        
        .data-table th {
            padding: 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .data-table td {
            padding: 16px;
            font-size: 14px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .data-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        .progress-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .progress-fill.success { background: #10b981; }
        .progress-fill.danger { background: #ef4444; }
        
        .sale-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }
        
        .btn-export {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
    </style>

    <div class="report-container">
        <div class="report-wrapper">
            <!-- Header -->
            <div class="report-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="report-title">
                            <i class="ri-bar-chart-box-line"></i>
                            รายงานสถิติการขายของ Sale
                        </h1>
                        <p class="report-subtitle">วิเคราะห์ประสิทธิภาพการทำงานและอัตราความสำเร็จของแต่ละคน</p>
                    </div>
                    <button wire:click="exportExcel" class="btn-export">
                        <i class="ri-file-excel-2-line"></i>
                        Export Excel
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <div class="filter-row">
                    <div>
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" wire:model.live="dateFrom">
                    </div>
                    <div>
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" wire:model.live="dateTo">
                    </div>
                    <div wire:ignore>
                        <label class="form-label">เลือก Sale (เลือกหลายคนได้)</label>
                        <select id="salesSelect" class="form-select" multiple>
                            @foreach($allSales as $sale)
                                <option value="{{ $sale->id }}">{{ $sale->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button wire:click="toggleShowAll" class="btn btn-outline-primary w-100">
                            <i class="ri-group-line me-2"></i>
                            {{ $showAllSales ? 'แสดงทุกคน' : 'กรองบางคน' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon primary">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="summary-content">
                        <h3>{{ number_format($summaryData['total'] ?? 0) }}</h3>
                        <p>ใบเสนอราคาทั้งหมด</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon warning">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="summary-content">
                        <h3>{{ number_format($summaryData['wait'] ?? 0) }}</h3>
                        <p>รออนุมัติ</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="summary-content">
                        <h3>{{ number_format($summaryData['success'] ?? 0) }}</h3>
                        <p>อนุมัติแล้ว ({{ $summaryData['success_rate'] ?? 0 }}%)</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon danger">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div class="summary-content">
                        <h3>{{ number_format($summaryData['cancel'] ?? 0) }}</h3>
                        <p>ไม่อนุมัติ ({{ $summaryData['cancel_rate'] ?? 0 }}%)</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon info">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="summary-content">
                        <h3>฿{{ number_format($summaryData['total_amount'] ?? 0, 2) }}</h3>
                        <p>ยอดขายรวม (อนุมัติ)</p>
                    </div>
                </div>
            </div>

            <!-- Sales Data Table -->
            <div class="data-card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sale</th>
                                <th class="text-center">ทั้งหมด</th>
                                <th class="text-center">รออนุมัติ</th>
                                <th class="text-center">อนุมัติแล้ว</th>
                                <th class="text-center">ไม่อนุมัติ</th>
                                <th>อัตราความสำเร็จ</th>
                                <th class="text-end">ยอดขายรวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesData as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="sale-avatar">
                                                {{ substr($data['sale']['name'] ?? 'N', 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">{{ $data['sale']['name'] ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $data['sale']['email'] ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $data['total'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $data['wait'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $data['success'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">{{ $data['cancel'] }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span style="font-size: 13px; color: #10b981; font-weight: 600;">
                                                    สำเร็จ: {{ $data['success_rate'] }}%
                                                </span>
                                                <span style="font-size: 13px; color: #ef4444; font-weight: 600;">
                                                    ยกเลิก: {{ $data['cancel_rate'] }}%
                                                </span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill success" style="width: {{ $data['success_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end" style="font-weight: 600; color: #10b981;">
                                        ฿{{ number_format($data['total_amount'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="ri-inbox-line" style="font-size: 48px; color: #d1d5db;"></i>
                                        <p class="text-muted mt-2">ไม่พบข้อมูล</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('#salesSelect').select2({
                placeholder: 'เลือก Sale (เลือกได้หลายคน)',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });

            // Update Livewire when selection changes
            $('#salesSelect').on('change', function() {
                let selectedValues = $(this).val() || [];
                @this.set('selectedSales', selectedValues);
            });

            // Listen for Livewire updates to reset Select2
            Livewire.on('salesUpdated', () => {
                $('#salesSelect').val([]).trigger('change');
            });

            // Reset Select2 when "แสดงทุกคน" is clicked
            window.addEventListener('showAllToggled', event => {
                if (event.detail.showAll) {
                    $('#salesSelect').val([]).trigger('change');
                }
            });
        });
    </script>
 
</div>
