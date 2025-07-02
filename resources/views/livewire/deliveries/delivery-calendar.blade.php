<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ $title }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Calendar navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button wire:click="previousMonth" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chevron-left"></i> เดือนก่อนหน้า
                        </button>
                        <h4 class="mb-0">{{ $currentMonthName }} {{ $buddhistYear }}</h4>
                        <button wire:click="nextMonth" class="btn btn-sm btn-outline-primary">
                            เดือนถัดไป <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Calendar -->
                    <div class="table-responsive">
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th class="text-center">อาทิตย์</th>
                                    <th class="text-center">จันทร์</th>
                                    <th class="text-center">อังคาร</th>
                                    <th class="text-center">พุธ</th>
                                    <th class="text-center">พฤหัสบดี</th>
                                    <th class="text-center">ศุกร์</th>
                                    <th class="text-center">เสาร์</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calendar as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            <td class="{{ !$day['isCurrentMonth'] ? 'bg-light text-muted' : '' }} 
                                                     {{ $day['isToday'] ? 'bg-soft-primary' : '' }}"
                                                style="height: 120px; width: 14.28%; vertical-align: top; position: relative;">
                                                
                                                <div class="d-flex justify-content-between p-1">
                                                    <span class="{{ $day['isToday'] ? 'font-weight-bold' : '' }}">{{ $day['day'] }}</span>
                                                </div>
                                                
                                                @if(isset($deliveriesByDate[$day['date']]))
                                                    <div class="calendar-events px-1 py-0">
                                                        @foreach($deliveriesByDate[$day['date']] as $delivery)
                                                            <div class="delivery-item p-1 mb-1 rounded bg-soft-info" onclick="window.location.href='{{ route('deliveries.printer', ['delivery' => $delivery['id']]) }}'">
                                                                <small class="d-block font-weight-bold text-truncate">
                                                                    #{{ $delivery['document_no'] ?? '-' }}
                                                                </small>
                                                                <small class="d-block text-truncate">
                                                                    <i class="ri-truck-line me-1"></i>{{ $delivery['truck_type'] ?? '-' }}
                                                                </small>
                                                                <small class="d-block text-truncate">
                                                                    <i class="ri-scale-line me-1"></i>{{ $delivery['total_weight'] ?? '0' }} กก.
                                                                </small>
                                                                <small class="d-block text-truncate">
                                                                    {!! order_delivery_status_badge($delivery['delivery_status']) !!}
                                                                </small>
                                                                <small class="d-block text-truncate text-muted">
                                                                    {{ $delivery['customer_name'] ?? '-' }}
                                                                </small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .calendar-table th {
            background-color: #f8f9fa;
            text-align: center;
        }
        .calendar-table td {
            border: 1px solid #dee2e6;
            height: 120px;
            width: 14.28%;
            vertical-align: top;
            position: relative;
        }
        .delivery-item {
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid #3b82f6;
        }
        .delivery-item:hover {
            background-color: #d1e6ff !important;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .bg-soft-primary {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        .bg-soft-info {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        .bg-light.text-muted {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }
        /* Style for badges in calendar */
        .delivery-item .badge {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            white-space: nowrap;
            display: inline-block;
            margin-top: 1px;
        }
    </style>
</div>
