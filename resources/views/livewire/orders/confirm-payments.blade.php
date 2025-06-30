<div>
    <br>
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#b45309;"><i class="mdi mdi-timer-sand"></i></div>
                    <div style="font-size:1.05rem; color:#b45309; font-weight:600;">รออนุมัติ</div>
                    <div style="font-size:2rem; font-weight:800; color:#b45309;">{{ $stats['pending_count'] }} <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#b45309;">{{ number_format($stats['pending_amount'], 2) }} บาท</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#059669;"><i class="mdi mdi-check-circle-outline"></i></div>
                    <div style="font-size:1.05rem; color:#059669; font-weight:600;">อนุมัติแล้ว</div>
                    <div style="font-size:2rem; font-weight:800; color:#059669;">{{ $stats['approved_count'] }} <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#059669;">{{ number_format($stats['approved_amount'], 2) }} บาท</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 text-center" style="background: #f9fafb; border-radius: 1.25rem; border: 1px solid #e5e7eb;">
                <div class="card-body py-3">
                    <div class="mb-1" style="font-size:1.6rem; color:#dc2626;"><i class="mdi mdi-close-circle-outline"></i></div>
                    <div style="font-size:1.05rem; color:#dc2626; font-weight:600;">ปฏิเสธแล้ว</div>
                    <div style="font-size:2rem; font-weight:800; color:#dc2626;">{{ $stats['rejected_count'] }} <span style="font-size:1rem; font-weight:400;">ใบ</span></div>
                    <div style="font-size:1.1rem; color:#dc2626;">-</div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h4 class="mb-3">รายการขอยืนยันสลิป</h4>
  


    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle" style="border-radius:10px; overflow:hidden; background:white;">
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
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->transfer_at)->format('d/m/Y H:i') }}</td>
                    <td><span class="badge bg-info text-dark" style="font-size:1rem;">{{ $payment->order->order_number ?? '-' }}</span></td>
                    <td><span style="font-weight:700; color:#059669;">{{ number_format($payment->amount, 2) }}</span></td>
                    <td>{{ $payment->sender_name }}</td>
                    <td>{{ $payment->bank_name }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $payment->slip_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">ดูสลิป</a>
                    </td>
                    <td>
                        @if($payment->status === 'รอยืนยันยอด')
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
                        <button class="btn btn-success btn-sm me-1" wire:click="confirm({{ $payment->id }})"><i class="mdi mdi-check"></i> ยืนยัน</button>
                        <button class="btn btn-danger btn-sm" wire:click="reject({{ $payment->id }})"><i class="mdi mdi-close"></i> ปฏิเสธ</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">ไม่มีรายการรอยืนยัน</td>
                </tr>
            @endforelse
        </tbody>
    </table>

          </div>
    </div>

</div>
