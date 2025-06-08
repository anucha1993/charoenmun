<div>
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
                        <button class="btn btn-success btn-sm" wire:click="confirm({{ $payment->id }})">✔ ยืนยัน</button>
                        <button class="btn btn-danger btn-sm" wire:click="reject({{ $payment->id }})">✖ ปฏิเสธ</button>
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
