<div>
    <div class="card row text-black">
        <div class="card-body">
            <!-- Invoice Detail-->
            <div class="clearfix">
                <div class="float-start">
                    <img src="/images/logo-cmc.png" class="mb-1" alt="dark logo" height="60">
                      <h4 class="m-0 mb-3">Quotation / ใบเสนอราคา</h4>
                </div>


                <div class="float-center">
                     {{-- <div class="text-center">
                        <h4 class="m-0 ">Quotation / ใบเสนอราคา</h4>
           
                    </div> --}}

                     <div class="float-end">

                        <img src="{{ route('qr.quotation', $quotation->id) }}" alt="QR" style="height:100px;">
                    </div>
                   
                </div>

            </div>


            <div class="row text-black">
                <div class="col-sm-6">
                    <div class="float-start">
                        <p><b>บริษัท เจริญมั่น คอนกรีต จำกัด(สำนักงานใหญ่)</b></p>
                        <p class=" fs-13"  style="margin-top: -10px">ที่อยู่ 99/35 หมู่ 9 ตำบลละหาร อำเภอบางบัวทอง จังหวัดนนทบุรี 11110 โทร
                            082-4789197 </br>
                            เลขประจำตัวผู้เสียภาษี 0125560015546
                        </p>
                    </div>

                </div><!-- end col -->
                <div class="col-sm-4 offset-sm-2">
                    <div class="mt-0 float-sm-end">
                        <p class="fs-13"><strong>วันที่เสนอราคา: </strong> &nbsp;&nbsp;&nbsp; {{date('d/m/Y',strtotime($quotation->quote_date))}}</p>
                        <p class="fs-13"><strong>เลขที่ใบเสนอราคา </strong> &nbsp;&nbsp;&nbsp; {{$quotation->quotation_number}}</p>
                        <p class="fs-13"><strong>ชื่อผู้ขาย (Sale) </strong> <span class="float-end">{{$quotation->sale->name}}</span></p>
                    </div>
                </div><!-- end col -->
            </div>
            <!-- end row -->

            <div class="row mt-4">
                <div class="col-6">
                    <h6 class="fs-14">ข้อมูลลูกค้า</h6>
                    <address>
                        {{$quotation->customer->customer_name}}<br>
                        {{$quotation->customer->customer_address}}<br>
                        {{$quotation->customer->customer_district_name.
                        ' '.$quotation->customer->customer_amphur_name.
                        ' '.$quotation->customer->customer_province_name.
                        ' '.$quotation->customer->customer_zipcode}}<br>
                        (+66) {{$quotation->customer->customer_phone}}
                    </address>
                </div> <!-- end col-->

                <div class="col-6">
                    <h6 class="fs-14">ที่อยู่จัดส่ง</h6>
                    <address>
                          {{$quotation->deliveryAddress->delivery_contact_name}} ({{$quotation->deliveryAddress->delivery_phone}})<br>
                        {{$quotation->deliveryAddress->delivery_number}}<br>
                        {{$quotation->deliveryAddress->delivery_district_name.
                        ' '.$quotation->deliveryAddress->delivery_amphur_name.
                        ' '.$quotation->deliveryAddress->delivery_province_name.
                        ' '.$quotation->deliveryAddress->delivery_zipcode}}<br>
                        
                    </address>
                </div> <!-- end col-->
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                            <thead class="border-top border-bottom bg-light-subtle border-light">
                                <tr>
                                    <th>ลำดับ</th>
                                    <th>จำนวน</th>
                                    <th>หน่วยนับ</th>
                                    <th>รายการสินค้า</th>
                                    <th>ราคาต่อหน่วย</th>
                                    <th class="text-end">จำนวนเงินรวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                  {{-- <tr>
                                    <td class="">1</td>
                                    <td>
                                        <b>Laptop</b> <br />
                                        Brand Model VGN-TXN27N/B
                                        11.1" Notebook PC
                                    </td>
                                    <td>1</td>
                                    <td>$1799.00</td>
                                    <td class="text-end">$1799.00</td>
                                </tr>
                                 --}}
                                @forelse ($quotation->items as $key => $item)
                                  <tr>
                                    <td class="">{{$key+1}}</td>
                                    <td>{{$item->quantity}}</td>
                                     <td>{{$item->product_unit}}</td>
                                    <td>
                                        <b>{{$item->product_name}}</b> <br />
                                         {{$item->product_detail}}
                                    </td>
                                    <td>{{number_format($item->unit_price,2)}}</td>
                                    <td class="text-end">{{number_format($item->total,2)}}</td>
                                </tr>
                                @empty
                                    
                                @endforelse
                              

                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-sm-6">
                    <div class="clearfix pt-3">
                        <h6 class="text-muted fs-14">หมายเหตุ:</h6>
                        <small>
                           {{$quotation->note}}
                        </small>
                    </div>
                </div> <!-- end col -->
                <div class="col-sm-6">
                    <div class="float-end mt-3 mt-sm-0">
                        <p><b>จำนวนเงินรวม :</b> <span class="float-end">{{$quotation->subtotal}}</span></p>
                        <p><b>ภาษีมูลค่าเพิ่ม:</b> <span class="float-end">{{$quotation->vat}}</span></p>
                        <p><b>จำนวนเงินทั้งสิ้น: &nbsp; </b> <span class="float-end">{{$quotation->grand_total}}</span></p>

                    </div>
                    <div class="clearfix"></div>
                </div> <!-- end col -->
            </div>
            <!-- end row-->

            <div class="d-print-none mt-4">
                <div class="text-center">
                    <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line"></i>
                        Print</a>
                    <a href="javascript: void(0);" class="btn btn-info">Submit</a>
                </div>
            </div>
            <!-- end buttons -->

        </div> <!-- end card-body-->
    </div> <!-- end card -->
</div> <!-- end col-->
</div>
<!-- end row -->
</div>


{{-- <script>
// เรียกเมื่อหน้าโหลดเสร็จ
document.addEventListener('DOMContentLoaded', () => {

    /** ฟังก์ชันกลับหน้าเดิม */
    const goBack = () => {
        // วิธี A: กลับหน้าเดิม
        history.back();

        // หรือ วิธี B: redirect ไป index โดยตรง
        // location.href = "{{ route('quotations.index') }}";
    };

    /* 1) เรียก dialog พิมพ์ทันที */
    window.print();

    /* 2) หลังกล่องพิมพ์ปิดแล้ว (กดพิมพ์หรือยกเลิก) → เรียก goBack */
    window.addEventListener('afterprint', goBack);   // Chrome/Edge
    window.onafterprint = goBack;                    // Safari/Firefox fallback
});
</script> --}}
