<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    <div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="submitPayment" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แจ้งชำระเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <strong>บิลหลัก:</strong> {{ $orderNumber }}<br>
                        <strong>บิลย่อย:</strong> {{ $orderDeliveryNumber }}
                    </div>
                    <hr>

                    @if (session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mb-3">
                        <label for="slip" class="form-label">แนบสลิปการโอน</label>
                        <input type="file" wire:model="slip" id="slip" accept="image/*" class="form-control">
                        @error('slip')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div wire:loading wire:target="slip" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">กำลังตรวจสอบสลิป...</div>
                    </div>

                    @if ($preview)
                        <div class="mb-3">
                            <label class="form-label">สลิปที่อัปโหลด:</label><br>
                            <img src="{{ $preview }}" class="img-fluid border" style="max-height: 300px;">
                        </div>
                    @endif



                    @if ($slipData)

                    <div class="border p-2 rounded bg-light mb-3">
                        <p><strong>ยอดรวมใบส่งของ:</strong> {{ number_format($deliveryTotal, 2) }} บาท</p>
                        <p><strong>ยอดที่ชำระแล้ว:</strong> {{ number_format($deliveryPaid, 2) }} บาท</p>
                        <p class="text-danger fw-bold"><strong>ยอดคงเหลือ:</strong> {{ number_format($deliveryRemain-$slipData['amount'], 2) }} บาท</p>
                    </div>

                        <div class="border rounded p-2 mt-2 bg-light">
                            <p><strong>ยอดเงิน:</strong> {{ number_format($slipData['amount'], 2) }} บาท</p>
                            <p><strong>ชื่อผู้โอน:</strong> {{ $slipData['sender_name'] ?? '-' }}</p>
                            <p><strong>ชื่อบัญชีผู้รับ:</strong> {{ $slipData['receiver_name'] ?? '-' }}</p>
                            <p><strong>วันที่โอน:</strong>
                                {{ \Carbon\Carbon::parse($slipData['transfer_at'])->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="slip">
                        <span wire:loading wire:target="slip">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            รอการตรวจสอบ...
                        </span>
                        <span wire:loading.remove wire:target="slip">
                            บันทึก
                        </span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('close-payment-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (modal) {
                modal.hide(); // ✅ ปิด modal
            }
        });
    });
</script>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('close-payment-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (modal) modal.hide();
        });
    });
</script>





