<div>
    <br>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">ออเดอร์ทั้งหมด</h5>
                    <p class="card-text fs-4">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
    
        {{-- Payment Status Cards --}}
        
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        @foreach ($paymentSummary as $status => $count)
                        <h6 class="card-title">{!! payment_status_badge($status) !!}</h6>
                        <p class="card-text fs-4">{{ $count }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
      
    
        {{-- Order Status Cards --}}
      
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        @foreach ($statusSummary as $status => $count)
                        <h6 class="card-title">{!! order_status_badge($status) !!}</h6>
                        <p class="card-text fs-4">{{ $count }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
       
    </div>

    <div class="card">
        <div class="card-header">
            <h4>รายการสั่งซื้อ</h4>
        </div>
        <div class="card-body">


    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>เลขที่ Order</th>
                <th>วันที่</th>
                <th>ชื่อลูกค้า</th>
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
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                    <td>{{ $order->customer->customer_name ?? '-' }}</td>
                    <td>{{ number_format($order->order_grand_total, 2) }}</td>
                    <td>
                        {!! payment_status_badge($order->payment_status)!!}
                       
                    </td>
                    <td>{!! order_status_badge($order->order_status) !!}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">ข้อมูลการขาย</a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $order->id }})">ลบ</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">ไม่มีข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}

    </div>

</div>
</div>

    <!-- Confirm Delete -->
    <script>
        function confirmDelete(id) {
            if (confirm('คุณแน่ใจว่าต้องการลบรายการนี้?')) {
                window.livewire.emit('deleteOrder', id);
            }
        }
    </script>
</div>
