<div>
{{-- resources/views/livewire/orders/show.blade.php --}}
<div class="container py-3">
    <div class="card">
        <div class="card-header">
           <h4>Order / ใบสั่งซื้อ</h4>
           <p class="float-end">เลขที่: <strong>{{ $order->order_number }}</strong></p>
            <p >วันที่: <strong>{{ $order->order_date->format('d/m/Y') }}</strong></p>
        </div>
        <div class="card-body">

    {{-- หัวใจ --}}
    <div class="row  float-end">
        <div class="col-12 ">
            <span>สถานะ: <strong> {!! order_status_badge($order->order_status) !!}</strong></span><br>
            <span>สถานะชำระเงิน: <strong>{!! payment_status_badge($order->payment_status) !!}</strong></span><br>
            <span>ภาษีมูลค่าเพิ่ม: <strong> {{ number_format($order->order_vat, 2) }} บาท</strong></span><br>
            <span>ส่วนลด: <strong> {{ number_format($order->order_discount, 2) }} บาท</strong></span><br>
            <span>จำนวนเงินทั้งสิ้น: <strong> {{ number_format($order->order_grand_total, 2) }} บาท</strong></span><br>
        </div>
        <div class="col-4 text-end">
            {{-- ปุ่มอนุมัติสำหรับ Order สินค้า (ถ้ามี) --}}
            @if ($order->status === 'open')
                <button wire:click="approveOrder" class="btn btn-primary">อนุมัติ Order</button>
            @endif
        </div>
    </div>

    {{-- ข้อมูลลูกค้า/ที่อยู่จัดส่ง --}}
    <div class="row ">
        <div class="col-6">
            <h6>ข้อมูลลูกค้า</h6>
            <address>
                {{ $order->customer->customer_name }}<br>
                {{ $order->customer->customer_address }}<br>
                {{ $order->customer->customer_district_name . ' ' .
                   $order->customer->customer_amphur_name . ' ' .
                   $order->customer->customer_province_name . ' ' .
                   $order->customer->customer_zipcode }}<br>
                (+66) {{ $order->customer->customer_phone }}
            </address>
        </div>
        <div class="col-6">
            <h6>ที่อยู่จัดส่ง</h6>
            @if ($order->deliveryAddress)
                <address>
                    {{ $order->deliveryAddress->delivery_contact_name }}
                    ({{ $order->deliveryAddress->delivery_phone }})<br>
                    {{ $order->deliveryAddress->delivery_number }}<br>
                    {{ $order->deliveryAddress->delivery_district_name . ' ' .
                       $order->deliveryAddress->delivery_amphur_name . ' ' .
                       $order->deliveryAddress->delivery_province_name . ' ' .
                       $order->deliveryAddress->delivery_zipcode }}<br>
                </address>
            @else
                <span class="text-muted">ไม่ได้ระบุที่อยู่จัดส่ง</span>
            @endif
        </div>
    </div>

    {{-- ตารางรายการสินค้า (Order Items) --}}
    <div class="row ">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
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
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_detail }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->product_unit }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- สรุปยอดรวม --}}
    {{-- <div class="row mb-4">
        <div class="col-sm-6 text-muted">
            <p>หมายเหตุ: {{ $order->note ?? '-' }}</p>
        </div>
        <div class="col-sm-6">
            <div class="float-end">
                <p><b>Sub-Total:</b>
                    <span class="float-end">{{ number_format($order->order_subtotal, 2) }}</span>
                </p>
                <p><b>Discount:</b>
                    <span class="float-end">{{ number_format($order->order_discount, 2) }}</span>
                </p>
                <p><b>VAT:</b>
                    <span class="float-end">{{ number_format($order->order_vat, 2) }}</span>
                </p>
                <h5><b>Grand Total:</b>
                    <span class="float-end">{{ number_format($order->order_grand_total, 2) }}</span>
                </h5>
            </div>
            <div class="clearfix"></div>
        </div>
    </div> --}}

    {{-- ส่วนสร้างและจัดการ Order Deliveries (ออร์เดอร์ย่อย) --}}
    <div class="row">
        <div class="col-12 mb-2">
            <h5>รายการจัดส่ง (Order Deliveries)</h5>
        </div>

        {{-- ตัวอย่าง Loop แสดง Order Deliveries --}}
 
            <div class="col-12 mb-3">
                <div class="card border-secondary">
                    {{-- <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between">
                            <span>Delivery #: {{ $delivery->order_delivery_number }}</span>
                            <span>วันที่: {{ $delivery->order_delivery_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>สถานะจัดส่ง: {{ ucfirst($delivery->order_delivery_status) }}</span>
                            <span>สถานะชำระเงิน: {{ ucfirst($delivery->payment_status) }}</span>
                        </div>
                    </div> --}}
                    <div class="card-body">
                        {{-- ตารางรายการสินค้าที่จัดส่งในรอบนี้ --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>วันที่จัดส่ง</th>
                                        <th>เลขที่บิลย่อย</th>
                                        <th>จำนวนเงินทั้งสิ้น</th>
                                        <th>สถานะจัดส่ง</th>
                                        <th>สถานะชำระเงิน</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                          @foreach ($order->deliveries as $key => $delivery)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{$delivery->order_delivery_date->format('d/m/Y')}}</td>
                                            <td>{{$delivery->order_delivery_number}}</td>
                                            <td>{{number_format($delivery->order_delivery_grand_total,2)}}</td>
                                            <td>{!! order_delivery_status_badge($delivery->order_delivery_status) !!}</td>
                                            <td>{!! payment_status_badge($delivery->payment_status) !!}</td>
                                            <td>
                                                <a href="{{route('deliveries.edit',[$delivery->order_id, $delivery->id])}}" target="_blank">แก้ไข</a>
                                            </td>

                                    @endforeach<
                                </tbody>
                            </table>
                        </div>
                        {{-- ปุ่มจัดการรายการย่อยถัดไป (ถ้ายัง pending) --}}
                        {{-- @if ($delivery->delivery_status === 'pending')
                            <div class="mt-2 text-end">
                                <button wire:click="markDeliveryAsDelivered({{ $delivery->id }})" 
                                        class="btn btn-success btn-sm">ยืนยันจัดส่งแล้ว</button>
                            </div>
                        @endif --}}
                    </div>
                </div>
            </div>
   

        {{-- ถ้ายังสามารถสร้าง Delivery รอบใหม่ได้ (ยังไม่ delivered ครบ) --}}
       @if ($order->order_status === 'open')
        <div class="col-12">
<button wire:click="createNewDelivery" class="btn btn-primary">
    <i class="ri-truck-line"></i> สร้างรอบจัดส่งใหม่
</button>
        </div>
    @endif
    </div>
</div>


        </div>
    </div>
    

</div>
