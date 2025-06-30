<div>
    <br>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);">
                <div class="card-body">
                    <h5 class="card-title" style="font-weight:700;">ออเดอร์ทั้งหมด</h5>
                    <p class="card-text fs-4" style="font-size:2rem;">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.10);">
                <div class="card-body">
                    @foreach ($paymentSummary as $status => $count)
                    <h6 class="card-title">{!! payment_status_badge($status) !!}</h6>
                    <p class="card-text fs-4">{{ $count }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.10);">
                <div class="card-body">
                    @foreach ($statusSummary as $status => $count)
                    <h6 class="card-title">{!! order_status_badge($status) !!}</h6>
                    <p class="card-text fs-4">{{ $count }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
        <div class="card-header" style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <h4 style="font-weight:700; color:#111827;">รายการสั่งซื้อ</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" style="background:white; border-radius:8px; overflow:hidden;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>เลขที่ Order</th>
                        <th>วันที่</th>
                        <th>ลูกค้า</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th>ยอดรวม</th>
                        <th>ชำระเงิน</th>
                        <th>สถานะจัดส่ง</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $index => $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $index }}</td>
                            <td><span style="font-weight:700; color:#3b82f6; background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 100%); padding:6px 12px; border-radius:6px; border:1px solid #93c5fd;">{{ $order->order_number }}</span></td>
                            <td>
                                <div style="font-weight:600; color:#111827;">{{ $order->order_date->format('d/m/Y') }}</div>
                                <div style="font-size:12px; color:#6b7280;">{{ $order->order_date->format('H:i น.') }}</div>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#111827;">{{ $order->customer->customer_name ?? '-' }}</div>
                                <div style="font-size:13px; color:#6b7280; background:#f3f4f6; padding:2px 8px; border-radius:4px; display:inline-block;">{{ $order->customer->customer_phone ?? '-' }}</div>
                                <div style="font-size:12px; color:#9ca3af; margin-top:4px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $order->customer->customer_address ?? '-' }}">{{ $order->customer->customer_address ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px; color:#374151; max-width:180px;">
                                    <div style="font-weight:600; color:#059669;">{{ optional($order->deliveryAddress)->delivery_contact_name ?? $order->customer->customer_name }}</div>
                                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">{{ optional($order->deliveryAddress)->delivery_phone ?? $order->customer->customer_phone }}</div>
                                    <div style="font-size:12px; color:#9ca3af; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ optional($order->deliveryAddress)->delivery_address ?? $order->customer->customer_address }}">{{ optional($order->deliveryAddress)->delivery_address ?? $order->customer->customer_address }}</div>
                                </div>
                            </td>
                            <td><span style="font-weight:700; color:#059669; font-size:17px; background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%); padding:8px 16px; border-radius:8px; border:1px solid #6ee7b7; display:inline-block;">{{ number_format($order->order_grand_total, 2) }}</span></td>
                            <td>
                                {!! payment_status_badge($order->payment_status)!!}
                                @if($order->payment_status === 'pending' || $order->payment_status === 'partial' || $order->payment_status === 'waiting_confirmation')
                                   <a href="{{ route('orders.payment.livewire', $order->id) }}" class="btn btn-sm btn-success mt-2">
                                    แจ้งชำระเงิน
                                </a>
                                @endif
                            </td>
                            <td>{!! order_status_badge($order->order_status) !!}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">ข้อมูลการขาย</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $order->id }})">ลบ</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">ไม่มีข้อมูล</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- แสดงสถานะการชำระเงินเฉพาะที่ Order (ไม่มี payment status ใน delivery) --}}

    <!-- Confirm Delete -->
    <script>
        function confirmDelete(id) {
            if (confirm('คุณแน่ใจว่าต้องการลบรายการนี้?')) {
                window.livewire.emit('deleteOrder', id);
            }
        }
    </script>
</div>
