<div>
    <br>
    <h4 class="mb-3">🔍 สแกนบิล</h4>
    <input type="text"
           wire:model.live.debounce.500ms="scanInput"
           autofocus
           class="form-control mb-4"
           placeholder="ยิง QR Code หรือกรอกเลขบิล">

    @if ($order)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📦 ข้อมูลบิลหลัก: {{ $order->order_number }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p><strong>👤 ลูกค้า:</strong> {{ $order->customer->customer_name ?? '-' }}</p>
                    <p><strong>💰 ยอดรวมทั้งหมด:</strong> {{ number_format($order->order_grand_total, 2) }} บาท</p>
                    <p><strong>💳 สถานะการชำระเงิน:</strong> {!! payment_status_badge($order->payment_status) !!}</p>
                    <p><strong>👤 ผู้ขาย:</strong> {{ $order->sale->name ?? '-' }}</p>
                </div>

                <hr>

                <h6 class="mb-3">📄 รายการบิลย่อย:</h6>

                @foreach ($order->deliveries as $delivery)
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>บิลย่อย: {{ $delivery->order_delivery_number }}</strong>
                            <span>{!! payment_status_badge($delivery->payment_status) !!}</span>
                        </div>
                        <p><strong>💰 ยอดรวม:</strong> {{ number_format($delivery->order_delivery_grand_total, 2) }} บาท</p>
                        <p><strong>🚚 สถานะจัดส่ง:</strong>
                            @if ($delivery->delivery_status === 'delivered')
                                <span class="badge bg-success">จัดส่งแล้ว</span>
                            @elseif ($delivery->delivery_status === 'in_progress')
                                <span class="badge bg-warning text-dark">กำลังจัดส่ง</span>
                            @else
                                <span class="badge bg-secondary">ยังไม่จัดส่ง</span>
                            @endif
                        </p>
                        <p><strong>📎 รายการสลิป:</strong></p>
                        <ul class="mb-0">
                            @forelse ($delivery->payments as $payment)
                                <li>
                                    {{ number_format($payment->amount, 2) }} บาท
                                    - {{ $payment->status }}
                                    @if ($payment->slip_path)
                                        | <a href="{{ asset('storage/' . $payment->slip_path) }}" target="_blank">ดูสลิป</a>
                                    @endif
                                </li>
                            @empty
                                <li class="text-muted">ยังไม่มีสลิปแนบ</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
