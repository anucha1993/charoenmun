<div>
    @php
        $copies = ['ต้นฉบับ (ลูกค้า)', 'สำเนา (คลังสินค้า)', 'สำเนา (พนักงานขับรถ)','สำเนา (ฝ่ายบัญชี)'];
        $copiesTotal = count($copies);
        $totalPages = ceil($delivery->deliveryItems->count() / 8) * $copiesTotal ;
        $loopIndex = 1;
        $showPricePages = request('show_price', []);
        
    @endphp
 <!-- Modal สำหรับเลือกหน้า -->
    <div class="modal fade" id="printPriceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เลือกสำเนาที่ต้องการแสดงราคา</h5>
                </div>
                <div class="modal-body">
                   
                    <form id="priceSelectionForm">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="0" id="showPrice0" >
                            <label class="form-check-label" for="showPrice0">หน้า 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="showPrice1" >
                            <label class="form-check-label" for="showPrice1">หน้า 2</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="2" id="showPrice2" >
                            <label class="form-check-label" for="showPrice2">หน้า 3</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" disabled checked>
                            <label class="form-check-label">หน้า 4 (แสดงราคาเสมอ)</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">กลับ</a>
                    <button type="button" class="btn btn-primary" onclick="applyPriceAndPrint()">พิมพ์เอกสาร</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-print-none text-center mb-4">
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#printPriceModal">
            <i class="ri-printer-line"></i> พิมพ์ใบส่งของ
        </button>
    </div>
    
@foreach ($copies as $copyIndex => $copyName)
    @foreach ($delivery->deliveryItems->chunk(8) as $chunkIndex => $chunk)
        <div class="card row text-black page-copy" >
            <div class="card-body">
                <!-- Invoice Detail-->
                <div class="clearfix">
                    <div class="float-start">
                        <img src="/images/logo-cmc.png" class="mb-0" alt="dark logo" height="60">
                        <h4 class="m-0 mb-0">Order Delivery / ใบส่งสินค้า</h4>
                    </div>

                    <div class="float-center">

                        <div class="float-end">

                            <img src="{{ route('qr.deliveries', $delivery->id) }}" alt="QR"
                                style="height:100px;"><br>
                            <small class="float-end">หน้า {{ $copyIndex + 1 }}/{{ $totalPages }}</small>
                        </div>

                    </div>

                </div>


                <div class="row text-black">
                    <div class="col-sm-6">
                        <div class="float-start">
                            <p><b>บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)</b></p>
                            <p class=" fs-13" style="margin-top: -10px">ที่อยู่ 99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง
                                จังหวัดนนทบุรี 11110 โทร
                                082-4789197 </br>
                                เลขประจำตัวผู้เสียภาษี 0125560015546
                            </p>
                        </div>
                    </div>

                    <div class="col-sm-4 offset-sm-2 mt-2">
                        <div class="mt-0 float-sm-end">
                            <span class="fs-13"><strong>วันที่เสนอราคา: </strong>
                                &nbsp;&nbsp;&nbsp;{{ date('d/m/Y', strtotime($delivery->quote_date)) }}</span> <br>
                            <span class="fs-13"><strong>เลขที่ใบส่งของ</strong>
                                &nbsp;&nbsp;&nbsp;{{ $delivery->order_delivery_number }}</span><br>
                            <span class="fs-13"><strong>เลขที่ใบสั่งซื้อ </strong>
                                &nbsp;&nbsp;&nbsp;{{ $delivery->order->order_number }}</span><br>
                            <span class="fs-13"><strong>ชื่อผู้ขาย (Sale) </strong><span class="float-end">
                                    {{ $delivery->sale->name }}</span></span><br>
                        </div>
                    </div>
                </div>

                <div class="row mt-1 ">
                    <div class="col-6">
                        <h6 class="fs-14">ข้อมูลลูกค้า</h6>
                        <address>
                            {{ $delivery->order->customer->customer_name }}<br>
                            {{ $delivery->order->customer->customer_address }}<br>
                            {{ $delivery->order->customer->customer_district_name .
                                ' ' .
                                $delivery->order->customer->customer_amphur_name .
                                ' ' .
                                $delivery->order->customer->customer_province_name .
                                ' ' .
                                $delivery->order->customer->customer_zipcode }}<br>
                            (+66) {{ $delivery->order->customer->customer_phone }}
                        </address>
                    </div> <!-- end col-->

                    <div class="col-6">
                        <h6 class="fs-14">ที่อยู่จัดส่ง</h6>
                        @if ($delivery->deliveryAddress)
                            <address>
                                {{ $delivery->deliveryAddress->delivery_contact_name }}
                                ({{ $delivery->deliveryAddress->delivery_phone }})<br>
                                {{ $delivery->deliveryAddress->delivery_number }}<br>
                                {{ $delivery->deliveryAddress->delivery_district_name .
                                    ' ' .
                                    $delivery->deliveryAddress->delivery_amphur_name .
                                    ' ' .
                                    $delivery->deliveryAddress->delivery_province_name .
                                    ' ' .
                                    $delivery->deliveryAddress->delivery_zipcode }}<br>

                            </address>
                        @else
                            <address>
                                {{ $delivery->order->customer->customer_contract_name }} (+66)
                                {{ $delivery->order->customer->customer_phone }} <br>
                                {{ $delivery->order->customer->customer_address }}<br>
                                {{ $delivery->order->customer->customer_district_name .
                                    ' ' .
                                    $delivery->order->customer->customer_amphur_name .
                                    ' ' .
                                    $delivery->order->customer->customer_province_name .
                                    ' ' .
                                    $delivery->order->customer->customer_zipcode }}<br>

                            </address>
                        @endif

                    </div> <!-- end col-->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-hover  mb-0 mt-0">
                                <thead class="border-top border-bottom border-start-0 border-end-0 border-danger">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>จำนวน</th>
                                        <th>หน่วยนับ</th>
                                        <th >รายการสินค้า</th>
                                        <th class="price-section">ราคาต่อหน่วย</th>
                                        <th class="text-end price-section">จำนวนเงินรวม</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($chunk as $item)
                                        <tr>
                                            <td>{{ $loopIndex++ }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->orderItem->product_unit }}</td>
                                            <td><b>{{ $item->orderItem->product_name }}</b>
                                                ({{ $item->orderItem->product_detail }})
                                            </td>
                                            <td class="price-section">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end price-section">{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
                <br>

                <div class="row ">
                    <div class="col-sm-6">
                        <div class="clearfix pt-3">
                            <h6 class="text-muted fs-14">หมายเหตุ:</h6>
                            <small>
                                {{ $delivery->order_deliver_note }}
                            </small>
                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="float-end mt-sm-0 price-section">
                            <p><b>จำนวนเงินรวม :</b> <span
                                    class="float-end">{{ number_format($delivery->order_delivery_subtotal, 2) }}</span>
                            </p>
                            <p><b>ส่วนลด:</b> <span
                                    class="float-end">{{ number_format($delivery->order_delivery_discount, 2) }}</span>
                            </p>
                            <p><b>ภาษีมูลค่าเพิ่ม:</b> <span
                                    class="float-end">{{ number_format($delivery->order_delivery_vat, 2) }}</span></p>
                            <p><b>จำนวนเงินทั้งสิ้น: &nbsp; </b> <span
                                    class="float-end">{{ number_format($delivery->order_delivery_grand_total, 2) }}</span>
                            </p>
                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>
                <!-- end row-->

                <hr>
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="clearfix">
                            <span>เงือนไขการระบสินค้า :</span><br>
                            <span>กรุณาตรวจสอบความถูกต้องของสินค้าและเซ็นรับสินค้าในวันที่ได้รับ
                                หากไม่มีการตรวจสอบหรือเซ็นรับสินค้า
                                ทางบริษัทขอสงวนสิทธิ์ในการรับผิดชอบต่อความผิดพลาดทุกกรณ</span><br>

                        </div>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-sm-6">
                        <div class="clearfix pt-4">
                            <span>ลงชื่อผู้รับสินค้า............................................................ผู้รับสินค้า</span><br>

                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="float-end mt-sm-0  pt-4">
                            <span>ลงชื่อผู้รับเงิน............................................................ผู้รับเงิน</span><br>

                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>

                {{-- <div class="d-print-none mt-4">
                    <div class="text-center">
                        <a href="javascript:window.print()" class="btn btn-danger"><i class="ri-printer-line"></i>
                            Print</a>

                    </div>
                </div> --}}
                <!-- end buttons -->

            </div> <!-- end card-body-->
        </div> <!-- end card -->

        {{-- <div class="d-print-none text-center mb-4">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#printPriceModal">
                <i class="ri-printer-line"></i> พิมพ์ใบส่งของ
            </button>
        </div> --}}


    @endforeach
    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach
    <!-- end row -->
    <style>
        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 150px;
        font-weight: bold;
        color: red;
        opacity: 0.1;
        z-index: 9999;
        pointer-events: none;
        font-style: italic;
        text-align: center;
        white-space: nowrap;
    }

    @media print {
        .watermark {
            display: none !important;
        }
    }
    </style>
<div class="watermark">ตัวอย่างก่อนพิมพ์</div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const printModal = new bootstrap.Modal(document.getElementById('printPriceModal'));
        printModal.show();
    });

    function applyPriceAndPrint() {
    const selectedPages = [];
    for (let i = 0; i <= 2; i++) {
        if (document.getElementById('showPrice' + i).checked) {
            selectedPages.push(i);
        }
    }

    const allCopies = document.querySelectorAll('.page-copy');
    allCopies.forEach((copyEl, index) => {
        const priceEls = copyEl.querySelectorAll('.price-section');
        if (index < 3) {
            // ✅ ถ้าอยู่ใน selectedPages ให้แสดง
            const show = selectedPages.includes(index);
            priceEls.forEach(el => el.style.display = show ? '' : 'none');
        } else {
            // ✅ หน้า 4 แสดงราคาเสมอ
            priceEls.forEach(el => el.style.display = '');
        }
    });

    const modal = bootstrap.Modal.getInstance(document.getElementById('printPriceModal'));
    modal.hide();

    setTimeout(() => window.print(), 300);
}


    window.addEventListener('afterprint', () => {
        history.back(); 
    });
</script>





