<div>
    <br>


    <div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-warning-subtle text-dark text-center">
            <div class="card-body">
                <h6 class="mb-1"><i class="mdi mdi-timer-sand"></i> ⏳ รออนุมัติ</h6>
                <h3>{{ $stats['pending_count'] }} <small>ใบ</small></h3>
                <p class="mb-0">{{ number_format($stats['pending_amount'], 2) }} บาท</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-success-subtle text-dark text-center">
            <div class="card-body">
                <h6 class="mb-1"><i class="mdi mdi-check-circle-outline"></i> ✅ อนุมัติแล้ว</h6>
                <h3>{{ $stats['approved_count'] }} <small>ใบ</small></h3>
                <p class="mb-0">{{ number_format($stats['approved_amount'], 2) }} บาท</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-danger-subtle text-dark text-center">
            <div class="card-body">
                <h6 class="mb-1"><i class="mdi mdi-close-circle-outline"></i> ❌ ปฏิเสธแล้ว</h6>
                <h3>{{ $stats['rejected_count'] }} <small>ใบ</small></h3>
                <p class="mb-0">-</p>
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

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>วันที่</th>
                <th>เลขที่บิลย่อย</th>
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
                    <td>{{ $payment->orderDelivery->order_delivery_number ?? '-' }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->sender_name }}</td>
                    <td>{{ $payment->bank_name }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $payment->slip_path) }}" target="_blank">ดูสลิป</a>
                    </td>
                    <td>
                        <span class="badge bg-warning">{{ $payment->status }}</span>
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm" wire:click="confirm({{ $payment->id }})">✔
                            ยืนยัน</button>
                        <button class="btn btn-danger btn-sm" wire:click="reject({{ $payment->id }})">✖
                            ปฏิเสธ</button>
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
