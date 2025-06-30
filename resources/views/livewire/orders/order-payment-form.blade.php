<div>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    แจ้งชำระเงินสำหรับ Order #{{ $order->order_number }}
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @php $stats = $this->getPaymentStats(); @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#fef9c3 0%,#fde68a 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#f59e42;"><i class="mdi mdi-cash-multiple"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#b45309;">ยอดสุทธิ Order</h6>
                                    <h2 style="font-weight:800; color:#b45309;">{{ number_format($stats['grand_total'], 2) }} <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#bbf7d0 0%,#6ee7b7 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#059669;"><i class="mdi mdi-check-circle-outline"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#059669;">ยอดที่ชำระแล้ว</h6>
                                    <h2 style="font-weight:800; color:#059669;">{{ number_format($stats['paid'], 2) }} <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border-0 text-center" style="background: linear-gradient(135deg,#fecaca 0%,#fca5a5 100%);">
                                <div class="card-body py-4">
                                    <div class="mb-2" style="font-size:2.2rem; color:#dc2626;"><i class="mdi mdi-cash-refund"></i></div>
                                    <h6 class="mb-1" style="font-weight:700; color:#dc2626;">ยอดคงค้าง</h6>
                                    <h2 style="font-weight:800; color:#dc2626;">{{ number_format($stats['outstanding'], 2) }} <small style="font-size:1rem; font-weight:400;">บาท</small></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $currentAmount = $mode === 'manual' ? ($manual['amount'] ?? 0) : ($slipData['amount'] ?? 0);
                    @endphp
                    @if (($currentAmount + $paidAmount) > $order->order_grand_total)
                        <div class="alert alert-warning">
                            <strong>แจ้งเตือน:</strong> ยอดที่ชำระครั้งนี้เกินยอดคงค้าง ระบบจะบันทึกเป็นยอดเกินชำระ
                        </div>
                    @endif
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="mode" id="modeApi" wire:model.live="mode" value="api">
                        <label class="btn btn-outline-primary" for="modeApi">ตรวจสอบสลิป</label>
                        <input type="radio" class="btn-check" name="mode" id="modeManual" wire:model.live="mode" value="manual">
                        <label class="btn btn-outline-secondary" for="modeManual">แมนนวลสลิป</label>
                    </div>
                    <div wire:loading wire:target="slip" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">กำลังตรวจสอบสลิป...</div>
                    </div>
                    <form wire:submit.prevent="submit" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="slip" class="form-label">แนบสลิปการโอน</label>
                            <input type="file" wire:model="slip" id="slip" accept="image/*" class="form-control">
                            @error('slip')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($preview)
                            <div class="mb-3">
                                <label class="form-label">สลิปที่อัปโหลด:</label><br>
                                <img src="{{ $preview }}" class="img-fluid border" style="max-height: 300px;">
                            </div>
                        @endif
                        @if ($mode === 'manual')
                            <div class="mb-3">
                                <label>ประเภทการชำระ</label>
                                <select class="form-select" wire:model.live="payment_type">
                                    <option value="transfer">เงินโอน</option>
                                    <option value="cash">รับเงินสด</option>
                                </select>
                            </div>
                            @if (isset($payment_type) && $payment_type == 'cash')
                                <div class="mb-3">
                                    <label>จำนวนเงิน</label>
                                    <input type="number" class="form-control" wire:model.defer="manual.amount">
                                </div>
                            @else
                                <div class="mb-3">
                                    <label>จำนวนเงิน</label>
                                    <input type="number" class="form-control" wire:model.defer="manual.amount">
                                </div>
                                <div class="mb-3">
                                    <label>ชื่อผู้โอน</label>
                                    <input type="text" class="form-control" wire:model.defer="manual.sender_name">
                                </div>
                                <div class="mb-3">
                                    <label>ชื่อธนาคาร</label>
                                    <input type="text" class="form-control" wire:model.defer="manual.bank_name">
                                </div>
                                <div class="mb-3">
                                    <label>วันที่โอน</label>
                                    <input type="datetime-local" class="form-control" wire:model.defer="manual.transfer_at">
                                </div>
                            @endif
                        @else
                            @if ($slipData)
                                <div class="border p-2 rounded bg-light mb-3">
                                    <div class="mb-2">
                                        <label>จำนวนเงิน</label>
                                        <input type="number" class="form-control" wire:model.defer="manual.amount" value="{{ $slipData['amount'] ?? 0 }}">
                                    </div>
                                    <p><strong>ชื่อผู้โอน:</strong> {{ $slipData['sender_name'] ?? '-' }}</p>
                                    <p><strong>ชื่อผู้รับ:</strong> {{ $slipData['receiver_name'] ?? '-' }}</p>
                                    <p><strong>วันที่โอน:</strong> {{ $slipData['transfer_at'] ?? '-' }}</p>
                                    <p><strong>ธนาคาร:</strong> {{ $slipData['bank_name'] ?? '-' }}</p>
                                </div>
                            @endif
                        @endif
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">ย้อนกลับ</a>
                            <button type="submit" class="btn btn-success">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
