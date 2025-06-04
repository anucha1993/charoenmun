{{-- resources/views/livewire/quotations/quotation-index.blade.php --}}
<div>
    <br>

    {{-- ───────── กราฟสถิติ ───────── --}}
    {{-- ใช้ wire:ignore เพื่อไม่ให้ Livewire มาแทนที่ DOM ของกราฟ --}}
    <div class="row mb-4" wire:ignore>
        {{-- Doughnut Chart: สัดส่วนใบเสนอราคาตามสถานะ --}}
        <div class="col-md-6">
            <div class="card ">
                <div class="card-header bg-primary text-white py-2">
                    สัดส่วนใบเสนอราคาตามสถานะ
                </div>
                <div class="card-body" style="position:relative; height:260px;">
                    <canvas id="statusChart" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- Bar Chart (vertical) Top 10 ลูกค้า --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white py-2">
                    Top 10 ลูกค้าที่มีใบเสนอราคา
                </div>
                <div class="card-body" style="position:relative; height:260px;">
                    <canvas id="customerChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ───────── Toolbar ค้นหา / กรอง ───────── --}}
    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <h3>
                รายการใบเสนอราคา
             <a href="{{route('quotations.create')}}" class="float-end btn btn-primary"> + เพิ่มใบเสนอราคา</a>
            </h3>
        </div>
        <div class="col-md-9">
            <input
                type="text"
                class="form-control"
                placeholder="ค้นหาเลขที่ใบเสนอราคาหรือชื่อลูกค้า..."
                wire:model.debounce.500ms.live="search"
            >
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="status">
                <option value="">-- สถานะทั้งหมด --</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}">
                        @if ($s === 'wait') รอดำเนินการ
                        @elseif ($s === 'success') ยืนยันแล้ว
                        @else ยกเลิก
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ───────── ตารางใบเสนอราคา ───────── --}}
    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-hover" style="font-size: 16px;">
                <thead class="table-light">
                    <tr>
                        <th>เลขที่</th>
                        <th>สถานะ</th>
                        <th>วันที่</th>
                        <th>ลูกค้า</th>
                        <th class="text-end">จำนวนเงิน</th>
                        <th>ผู้ขาย</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotes as $q)
                        <tr>
                            <td>{{ $q->quote_number }}</td>
                            <td>{!! quote_status_badge($q->quote_status) !!}</td>
                            <td>{{ $q->quote_date->format('d/m/Y') }}</td>
                            <td>{{ $q->customer->customer_name }}</td>
                            <td class="text-end">{{ number_format($q->quote_grand_total, 2) }}</td>
                            <td>{{ $q->sale->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('quotations.print', $q->id) }}" class="text-purple me-2">
                                    <i class="ri-printer-line" style="font-size: 20px;"></i> ปริ้น
                                </a>
                                <a href="{{ route('quotations.edit', $q) }}" class="text-info me-2">
                                    <i class="mdi mdi-file-sign" style="font-size: 20px;"> </i>แก้ไข
                                </a>
                                <a wire:click="delete({{ $q->id }})" class="text-danger" onclick="return confirm('ยืนยันลบ?')">
                                    <i class="mdi mdi-trash-can" style="font-size: 20px;"> </i>ลบ
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                ไม่พบข้อมูลใบเสนอราคา
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-2">
                {{ $quotes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('>>> DOMContentLoaded - เริ่มวาดกราฟ');

            // 1) ดึงข้อมูล initial ที่ฝังมาจาก PHP
            const initialStatusData   = @json($statusData ?? []);
            const initialCustomerData = @json($customerData ?? []);

            console.log('initialStatusData =', initialStatusData);
            console.log('initialCustomerData =', initialCustomerData);

            // 2) จับ <canvas> (เตรียมตัวแปรไว้ข้างนอก เพื่ออัปเดตซ้ำได้)
            const ctxStatus   = document.getElementById('statusChart');
            const ctxCustomer = document.getElementById('customerChart');

            console.log('พบ statusChart element =', ctxStatus);
            console.log('พบ customerChart element =', ctxCustomer);

            let statusChart, customerChart;

            function renderCharts(statusData, customerData) {
                console.log('renderCharts() ถูกเรียกด้วย', statusData, customerData);

                // ถ้าไม่มี element หรือข้อมูลไม่ครบ ก็ไม่ต้องสร้าง
                if (!ctxStatus || !ctxCustomer) {
                    console.warn('Canvas element ยังไม่พร้อม:', ctxStatus, ctxCustomer);
                    return;
                }
                if (!statusData?.labels || !Array.isArray(statusData.labels)) {
                    console.warn('statusData ไม่ถูกต้อง:', statusData);
                    return;
                }
                if (!customerData?.labels || !Array.isArray(customerData.labels)) {
                    console.warn('customerData ไม่ถูกต้อง:', customerData);
                    return;
                }

                // ลบกราฟเก่าถ้ามี
                if (statusChart)   statusChart.destroy();
                if (customerChart) customerChart.destroy();

                // สร้าง Doughnut Chart (สัดส่วนตามสถานะ)
                statusChart = new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: statusData.labels,
                        datasets: [{
                            data: statusData.data,
                            backgroundColor: ['#28a745','#007bff','#dc3545']
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth:12, padding:12 }
                            }
                        }
                    }
                });
                console.log('สร้าง statusChart เสร็จ:', statusChart);

                // สร้าง Bar Chart แนวตั้ง (Top 10 ลูกค้า)
                customerChart = new Chart(ctxCustomer, {
                    type: 'bar',
                    data: {
                        labels: customerData.labels,
                        datasets: [{
                            label: 'จำนวนใบเสนอราคา',
                            data: customerData.data,
                            backgroundColor: '#17a2b8'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        },
                        maintainAspectRatio: false
                    }
                });
                console.log('สร้าง customerChart เสร็จ:', customerChart);
            }

            renderCharts(initialStatusData, initialCustomerData);
            window.addEventListener('chart-update', event => {
                console.log('>>> เกิด chart-update event raw:', event.detail);

                const payload = event.detail.data ?? event.detail;
                console.log('>>> normalised payload =', payload);

                if (!payload?.status || !payload?.customers) {
                    console.warn('payload ไม่มี properties status หรือ customers:', payload);
                    return;
                }
                renderCharts(payload.status, payload.customers);
            });

            Livewire.hook('message.processed', (message, component) => {
                console.log('>>> Livewire re-render เสร็จ (message.processed)');
            });
        });
    </script>


