<div class="container py-3">


     <div class="">
            <h4 class="mb-3">🔍 สแกนบิล</h4>

            <input type="text" wire:model.live.debounce.500ms="scanInput" autofocus class="form-control mb-4"
                placeholder="ยิง QR Code หรือกรอกเลขบิล">

            @if (!$order)
                <div class="d-flex align-items-center justify-content-center border rounded bg-white py-5"
                    style="height: 300px;">
                    <div class="text-center text-muted">
                       <i class="ri-qr-scan-2-line" style="font-size: 100px;"></i>
                        <p class="mt-3 mb-0 text-success">กรุณายิง QR Code หรือกรอกเลขบิล</p>
                        <small class="text-secondary">ระบบจะแสดงข้อมูลใบสั่งซื้อที่เกี่ยวข้อง</small>
                    </div>
                </div>
        
            @endif
        </div>

    @if ($order)
        @php
            $totalConfirmed = $order->deliveries->flatMap->payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
            $totalWaiting = $order->deliveries->flatMap->payments->where('status', 'รอยืนยันยอด')->sum('amount');
        @endphp



        <div class="card mb-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📄 ใบสั่งซื้อ: {{ $order->order_number }}</h5>
                <span>{{ $order->order_date->format('d/m/Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-0">
                    <div class="col-md-6">
                        <h6>👤 ข้อมูลลูกค้า</h6>
                        <p class="mb-1"><strong>{{ $order->customer->customer_name }}</strong></p>
                        <p class="mb-1">{{ $order->customer->customer_address }}</p>
                        <p class="mb-1">
                            {{ $order->customer->customer_district_name }},
                            {{ $order->customer->customer_amphur_name }},
                            {{ $order->customer->customer_province_name }} {{ $order->customer->customer_zipcode }}
                        </p>
                        <p>โทร: {{ $order->customer->customer_phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>🚚 ที่อยู่จัดส่ง</h6>
                        @if ($order->deliveryAddress)
                            <p class="mb-1">{{ $order->deliveryAddress->delivery_contact_name }}
                                ({{ $order->deliveryAddress->delivery_phone }})</p>
                            <p class="mb-1">{{ $order->deliveryAddress->delivery_number }}</p>
                            <p>
                                {{ $order->deliveryAddress->delivery_address }}
                            </p>
                        @else
                            <span class="text-muted">ไม่ได้ระบุที่อยู่จัดส่ง</span>
                        @endif
                    </div>
                </div>
                <hr>

                <div class="row mb-0">
                    <div class="col-md-6">
                        <h6>💳 สถานะการเงิน</h6>
                        <p>สถานะใบสั่งซื้อ: {!! order_status_badge($order->order_status) !!}</p>
                        <p>สถานะชำระเงิน: {!! payment_status_badge($order->payment_status) !!}</p>
                        <p>รอยืนยันยอด: <span class="text-warning">{{ number_format($totalWaiting, 2) }} บาท</span></p>
                        <p>ชำระแล้ว: <span class="text-success">{{ number_format($totalConfirmed, 2) }} บาท</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>💰 สรุปยอด</h6>
                        <p>ส่วนลด: {{ number_format($order->order_discount, 2) }} บาท</p>
                        <p>VAT: {{ number_format($order->order_vat, 2) }} บาท</p>
                        <p><strong>รวมทั้งหมด: {{ number_format($order->order_grand_total, 2) }} บาท</strong></p>
                    </div>
                </div>

                <h6 class="mb-2">📦 รายการสินค้า</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>สินค้า</th>
                                <th>รายละเอียด</th>
                                <th>จำนวนสั่ง</th>
                                <th>หน่วย</th>
                                <th>ราคา/หน่วย</th>
                                <th class="text-end">ยอดรวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $idx => $item)
                                @php
                                    $delivered = $deliveredQtyMap[$item->product_id] ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_detail }}</td>
                                    <td>{{ $item->quantity }} {{ $delivered > 0 ? '(' . $delivered . ')' : '' }}</td>
                                    <td>{{ $item->product_unit }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4">🚚 รายการจัดส่ง</h6>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เลขบิลย่อย</th>
                            <th>ยอดรวม</th>
                            <th>สถานะจัดส่ง</th>
                            <th>สถานะชำระเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->deliveries as $delivery)
                            <tr>
                                <td>{{ $delivery->order_delivery_date->format('d/m/Y') }}</td>
                                <td>{{ $delivery->order_delivery_number }}</td>
                                <td>{{ number_format($delivery->order_delivery_grand_total, 2) }}</td>
                                <td>{!! order_delivery_status_badge($delivery->order_delivery_status) !!}</td>
                                <td>{!! payment_status_badge($delivery->payment_status) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
   

    @endif



</div>
