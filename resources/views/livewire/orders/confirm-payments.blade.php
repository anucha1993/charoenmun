<div class="row">

    <!-- Sidebar Filter -->
    <div class="col-md-3 mb-3 mt-3">
            <!-- QR/Order Search Input -->
            <div class="mb-3">
               <input type="text" class="form-control" placeholder="ยิง QR หรือกรอกเลขที่ออเดอร์ แล้วกด Enter"
                 wire:model.live.debounce.500ms="searchOrderInput" autofocus>
                <div style="font-size:0.95rem; color:#888; margin-top:2px;">
                    <b>DEBUG (frontend):</b> searchOrder = <code>{{ $searchOrder }}</code> | Input = <code>{{ $searchOrderInput }}</code>
                </div>
            </div><div class="list-group">
            <button class="list-group-item list-group-item-action {{ $filterType === 'today' ? 'active' : '' }}"
                wire:click="setFilter('today')">
                <i class="mdi mdi-calendar-today"></i> สลิปวันนี้
            </button>

            <button class="list-group-item list-group-item-action {{ $filterType === 'pending' ? 'active' : '' }}"
                wire:click="setFilter('pending')">
                <i class="mdi mdi-timer-sand"></i> ค้างรออนุมัติ (ไม่ใช่วันนี้)
            </button>
            <button class="list-group-item list-group-item-action {{ $filterType === 'approved' ? 'active' : '' }}"
                wire:click="setFilter('approved')">
                <i class="mdi mdi-check-circle-outline"></i> ยืนยันสลิปแล้ว
            </button>
            <button class="list-group-item list-group-item-action {{ $filterType === 'rejected' ? 'active' : '' }}"
                wire:click="setFilter('rejected')">
                <i class="mdi mdi-close-circle-outline"></i> ปฏิเสธแล้ว
            </button>
        </div>
    </div>
    <!-- Main Content -->
    <div class="col-md-9">
        <br>
        <div class="row g-3 mb-4 justify-content-center">
            <div class="col-12 col-md-4">
                <div class="card border-0 text-center"
                    style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                    <div class="card-body py-3">
                        <div class="mb-1" style="font-size:1.6rem; color:#b45309;"><i class="mdi mdi-timer-sand"></i>
                        </div>
                        <div style="font-size:1.05rem; color:#b45309; font-weight:600;">รออนุมัติ</div>
                        <div style="font-size:2rem; font-weight:800; color:#b45309;">{{ $stats['pending_count'] }} <span
                                style="font-size:1rem; font-weight:400;">ใบ</span></div>
                        <div style="font-size:1.1rem; color:#b45309;">{{ number_format($stats['pending_amount'], 2) }}
                            บาท</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 text-center"
                    style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                    <div class="card-body py-3">
                        <div class="mb-1" style="font-size:1.6rem; color:#059669;"><i
                                class="mdi mdi-check-circle-outline"></i></div>
                        <div style="font-size:1.05rem; color:#059669; font-weight:600;">อนุมัติแล้ว</div>
                        <div style="font-size:2rem; font-weight:800; color:#059669;">{{ $stats['approved_count'] }}
                            <span style="font-size:1rem; font-weight:400;">ใบ</span>
                        </div>
                        <div style="font-size:1.1rem; color:#059669;">{{ number_format($stats['approved_amount'], 2) }}
                            บาท</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 text-center"
                    style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                    <div class="card-body py-3">
                        <div class="mb-1" style="font-size:1.6rem; color:#dc2626;"><i
                                class="mdi mdi-close-circle-outline"></i></div>
                        <div style="font-size:1.05rem; color:#dc2626; font-weight:600;">ปฏิเสธแล้ว</div>
                        <div style="font-size:2rem; font-weight:800; color:#dc2626;">{{ $stats['rejected_count'] }}
                            <span style="font-size:1rem; font-weight:400;">ใบ</span>
                        </div>
                        <div style="font-size:1.1rem; color:#dc2626;">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">

            <h4 class="mb-3">รายการขอยืนยันสลิป</h4>
            <!-- QR/Order Search Input -->
            {{-- <div class="mb-3">
               <input type="text" class="form-control" placeholder="ยิง QR หรือกรอกเลขที่ออเดอร์ แล้วกด Enter"
                wire:model.live.debounce.500ms="searchOrder" wire:keydown.enter.prevent="searchOrder" autofocus>
                <div style="font-size:0.95rem; color:#888; margin-top:2px;">
                    <b>DEBUG (frontend):</b> searchOrder = <code>{{ $searchOrder }}</code>
                </div>
            </div> --}}


            <table class="table table-bordered table-hover align-middle"
                style="border-radius:10px; overflow:hidden; background:white;">
                <thead class="table-light" style="font-size:1.05rem;">
                    <tr style="background:linear-gradient(135deg,#f8fafc 0%,#e0e7e0 100%);">
                        <th>วันที่</th>
                        <th>เลขที่ออเดอร์</th>
                        <th>ยอดเงิน</th>
                        <th>ผู้โอน</th>
                        <th>บัญชีปลายทาง</th>
                        <th>สลิป</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                        <th>ออเดอร์</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->transfer_at)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-info text-dark"
                                    style="font-size:1rem;">{{ $payment->order->order_number ?? '-' }}</span></td>
                            <td><span
                                    style="font-weight:700; color:#059669;">{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td>{{ $payment->sender_name }}</td>
                            <td>{{ $payment->bank_name }}</td>
                            <td>
                                <a href="{{ asset('storage/' . $payment->slip_path) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">ดูสลิป</a>
                            </td>
                            <td>
                                @if ($payment->status === 'รอยืนยันยอด')
                                    <span class="badge bg-warning text-dark">{{ $payment->status }}</span>
                                @elseif($payment->status === 'ชำระเงินแล้ว')
                                    <span class="badge bg-success">{{ $payment->status }}</span>
                                @elseif($payment->status === 'ปฏิเสธ')
                                    <span class="badge bg-danger">{{ $payment->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($payment->status === 'รอยืนยันยอด')
                                    <button class="btn btn-success btn-sm me-1"
                                        wire:click="confirm({{ $payment->id }})"><i class="mdi mdi-check"></i>
                                        ยืนยัน</button>
                                    @if ($rejectingId === $payment->id)
                                        <div class="mt-2">
                                            <textarea class="form-control mb-2" wire:model.defer="rejectReason" rows="2" placeholder="กรอกเหตุผลการปฏิเสธ"></textarea>
                                            <button class="btn btn-danger btn-sm me-1"
                                                wire:click="reject">ยืนยันปฏิเสธ</button>
                                            <button class="btn btn-secondary btn-sm"
                                                wire:click="hideRejectModal">ยกเลิก</button>
                                        </div>
                                    @else
                                        <!-- เรียกใช้ฟังก์ชัน confirmReject ด้วย SweetAlert2 -->
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmRejectSweetAlert('{{ $payment->id }}')" type="button">
                                            <i class="mdi mdi-close"></i> ปฏิเสธ
                                        </button>
                                    @endif
                                @elseif($payment->status === 'ปฏิเสธ')
                                    <span class="text-danger small">เหตุผล: {{ $payment->reject_reason }}</span><br>
                                    <button class="btn btn-warning btn-sm mt-1"
                                        wire:click="setPending({{ $payment->id }})">
                                        <i class="mdi mdi-undo"></i> เปลี่ยนเป็นรออนุมัติ
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if ($payment->order)
                                    <a href="{{ route('orders.show', ['order' => $payment->order->id]) }}"
                                        class="btn btn-outline-info btn-sm" target="_blank">
                                        <i class="mdi mdi-eye"></i> ดูรายละเอียด
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <!-- แก้ไข colspan ให้ถูกต้องตามจำนวนคอลัมน์ทั้งหมด 9 คอลัมน์ -->
                            <td colspan="9" class="text-center text-muted">ไม่มีรายการรอยืนยัน</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN and the JavaScript for confirmRejectSweetAlert function -->
<!-- ควรวางบล็อกสคริปต์นี้ไว้ที่ท้ายสุดของไฟล์ Blade ของคุณเสมอ (ก่อนแท็ก </body> ปิด) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
        // เมื่อ Livewire ถูกโหลดและพร้อมใช้งาน
        window.confirmRejectSweetAlert = function(paymentId) {
            Swal.fire({
                title: 'กรุณาระบุเหตุผลการปฏิเสธ',
                input: 'textarea',
                inputPlaceholder: 'กรอกเหตุผล...',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันปฏิเสธ',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกเหตุผล';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // เรียกใช้ Livewire method 'rejectWithReason'
                    // @this คือการอ้างถึง Livewire component ปัจจุบัน (ใน Livewire v3+ ใช้วิธีนี้)
                    @this.call('rejectWithReason', paymentId, result.value);
                }
            });
        };
    });
</script>
