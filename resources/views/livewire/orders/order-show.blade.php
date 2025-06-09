<div>
    {{-- resources/views/livewire/orders/show.blade.php --}}

    @php
        $totalConfirmed = $order->deliveries->flatMap->payments->where('status', 'ชำระเงินแล้ว')->sum('amount');
        $totalWaiting = $order->deliveries->flatMap->payments->where('status', 'รอยืนยันยอด')->sum('amount');
    @endphp
    <div class="container py-3">
        <div class="card">
            <div class="card-header">
                <h4>Order / ใบสั่งซื้อ</h4>
                <p class="float-end">เลขที่: <strong>{{ $order->order_number }}</strong></p>
                <p>วันที่: <strong>{{ $order->order_date->format('d/m/Y') }}</strong></p>
                <p class="float-end">ชำระเงินแล้ว: <strong class="text-success">{{ number_format($totalConfirmed) }} บาท</strong></p>
                <p>รอยืนยันยอด: <strong class="text-warning">{{ number_format($totalWaiting) }} บาท</strong></p>
            </div>
            <div class="card-body">

                {{-- หัวใจ --}}
                <div class="row  float-end">


                    <div class="col-12 ">
                        <span>สถานะ: <strong> {!! order_status_badge($order->order_status) !!}</strong></span><br>
                        <span>สถานะชำระเงิน: <strong>{!! payment_status_badge($order->payment_status) !!}</strong></span><br>
                        <span>ภาษีมูลค่าเพิ่ม: <strong> {{ number_format($order->order_vat, 2) }}
                                บาท</strong></span><br>
                        <span>ส่วนลด: <strong> {{ number_format($order->order_discount, 2) }} บาท</strong></span><br>
                        <span>จำนวนเงินทั้งสิ้น: <strong> {{ number_format($order->order_grand_total, 2) }}
                                บาท</strong></span><br>

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
                        <h4>ข้อมูลลูกค้า</h4>
                        <address>
                            {{ $order->customer->customer_name }}<br>
                            {{ $order->customer->customer_address }}<br>
                            {{ $order->customer->customer_district_name .
                                ' ' .
                                $order->customer->customer_amphur_name .
                                ' ' .
                                $order->customer->customer_province_name .
                                ' ' .
                                $order->customer->customer_zipcode }}<br>
                            (+66) {{ $order->customer->customer_phone }}
                        </address>
                    </div>
                    <div class="col-6">
                        <h4>ที่อยู่จัดส่ง</h4>
                        @if ($order->deliveryAddress)
                            <address>
                                {{ $order->deliveryAddress->delivery_contact_name }}
                                ({{ $order->deliveryAddress->delivery_phone }})<br>
                                {{ $order->deliveryAddress->delivery_number }}<br>
                                {{ $order->deliveryAddress->delivery_district_name .
                                    ' ' .
                                    $order->deliveryAddress->delivery_amphur_name .
                                    ' ' .
                                    $order->deliveryAddress->delivery_province_name .
                                    ' ' .
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
                                            <td>
                                                @php
                                                    $delivered = $deliveredQtyMap[$item->product_id] ?? 0;
                                                @endphp
                                                {{ $item->quantity }}

                                                @if ($delivered > 0)
                                                    ({{ $delivered }})
                                                @endif
                                            </td>
                                            <td>{{ $item->product_unit }}</td>
                                            <td>

                                                {{ number_format($item->unit_price, 2) }}


                                            </td>
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
                <div class="row">
                    <div class="col-12 mb-2">
                        <h5>รายการจัดส่ง (Order Deliveries)</h5>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover mb-0"
                                        style="font-size: 14px">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>วันที่จัดส่ง</th>
                                                <th>เลขที่บิลย่อย</th>
                                                <th>จำนวนเงินทั้งสิ้น</th>
                                                <th>ชำระเงินแล้ว</th>
                                                <th>รอยืนยันยอด</th>
                                                <th>สถานะจัดส่ง</th>
                                                <th>สถานะชำระเงิน</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->deliveries as $key => $delivery)
                                                @php
                                                    $confirmed = $delivery->payments
                                                        ->where('status', 'ชำระเงินแล้ว')
                                                        ->sum('amount');
                                                    $waiting = $delivery->payments
                                                        ->where('status', 'รอยืนยันยอด')
                                                        ->sum('amount');
                                                @endphp

                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $delivery->order_delivery_date->format('d/m/Y') }}</td>
                                                    <td>{{ $delivery->order_delivery_number }}</td>

                                                    <td>{{ number_format($delivery->order_delivery_grand_total, 2) }}
                                                    </td>

                                                    <td class="text-succcess">{{ number_format($confirmed, 2) }}</td>
                                                    <td class="text-warning">{{ number_format($waiting, 2) }}</td>
                                                    <td>{!! order_delivery_status_badge($delivery->order_delivery_status) !!}</td>
                                                    <td>{!! payment_status_badge($delivery->payment_status) !!}</td>
                                                    
                                                    <td>
                                                        <a href=""> 🚚 การจัดส่ง</a> | 
                                                        <a href="{{ route('deliveries.printer', $delivery->id) }}"
                                                            class="text-pink"><i class="mdi mdi-printer"></i> พิมพ์</a>
                                                        |

                                                        <a href="javascript: void(0);" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                            wire:click="$dispatch('open-payment-modal', { orderId: {{ $order->id }}, deliveryId: {{ $delivery->id }} })">
                                                            <i class="mdi mdi-cash-multiple "></i> แจ้งชำระเงิน
                                                        </a>
                                                        |
                                                        <a href="{{ route('deliveries.edit', [$delivery->order_id, $delivery->id]) }}"
                                                            class="text-dark" target="_blank"><i
                                                                class="mdi mdi-content-save-edit-outline"></i> แก้ไข</a>

                                                        |
                                                        <a href="" class="text-danger"><i
                                                                class="mdi mdi-trash-can"></i> ลบ</a>

                                                    </td>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

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


    <!-- Modal สำหรับเลือกหน้า -->
    <div class="modal fade" id="printPriceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เลือกสำเนาที่ต้องการแสดงราคา</h5>
                </div>
                <div class="modal-body">
                    <form id="priceSelectionForm" method="GET">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="0" id="showPrice0">
                            <label class="form-check-label" for="showPrice0">หน้า 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="showPrice1">
                            <label class="form-check-label" for="showPrice1">หน้า 2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="2" id="showPrice2">
                            <label class="form-check-label" for="showPrice2">หน้า 3</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" disabled checked>
                            <label class="form-check-label">หน้า 4 (แสดงราคาเสมอ)</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary"
                        onclick="applyPriceAndRedirect({{ $delivery->id }})">พิมพ์เอกสาร</button>
                </div>
            </div>
        </div>
    </div>




    <livewire:orders.payment-modal />
</div>

{{-- <script>
    document.addEventListener("livewire:init", () => {
        Livewire.on("show-payment-modal", () => {
            const modal = new bootstrap.Modal(document.getElementById("paymentModal"));
            modal.show();
        });
    });
</script> --}}

{{-- <script>
    document.addEventListener("livewire:init", () => {
        Livewire.on("close-payment-modal", () => {
            const modal = new bootstrap.Modal(document.getElementById("paymentModal"));
            modal.hide();
        });
    });
</script> --}}


<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('close-payment-modal', () => {
            console.log('close-payment-modal received'); // ตรวจสอบว่า event ถึงจริงไหม
            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (modal) modal.hide();
        });
    });
</script>

<script>
    function openPrintPreview(deliveryId) {
        const selected = [];

        for (let i = 0; i <= 2; i++) {
            const checkbox = document.getElementById('showPrice' + i);
            if (checkbox && checkbox.checked) {
                selected.push(i);
            }
        }

        // สร้าง query string เช่น show_price[]=0&show_price[]=1
        const query = selected.map(i => `show_price[]=${encodeURIComponent(i)}`).join('&');

        // สร้าง URL ไปยัง route delivery/print
        const printUrl = `{{ url('deliveries') }}/${deliveryId}/print?${query}`;

        // เปิดในแท็บใหม่
        window.open(printUrl, '_blank');

        // ปิด modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('printPriceModal'));
        if (modal) modal.hide();
    }
</script>



{{-- <script>
    window.addEventListener('open-print', event => {
        window.open(event.detail.url, '_blank');
    });
</script> --}}
