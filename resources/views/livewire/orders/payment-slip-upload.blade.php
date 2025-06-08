<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <form wire:submit.prevent="uploadAndVerify" enctype="multipart/form-data">
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
    
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    
        <input type="file" wire:model="slipImage" accept="image/*">
        @error('slipImage') <span class="text-danger">{{ $message }}</span> @enderror
    
        <button type="submit" class="btn btn-primary mt-2">ตรวจสอบสลิป</button>
    </form>
    @if (!empty($slipData))
<div class="card mt-4 p-3 border">
    <h5>📄 รายละเอียดสลิป</h5>
    <ul class="list-unstyled">
        <li><strong>ยอดเงิน:</strong> {{ number_format($slipData['amount'], 2) }} บาท</li>
        <li><strong>วันเวลาโอน:</strong> {{ \Carbon\Carbon::parse($slipData['dateTime'])->format('d/m/Y H:i') }}</li>
        <li><strong>เลขอ้างอิงสลิป:</strong> {{ $slipData['referenceId'] }}</li>
        <li><strong>รหัสธนาคารผู้รับ:</strong> {{ $slipData['receiver']['bank']['id'] ?? '-' }}</li>
        <li><strong>ชื่อธนาคารผู้รับ:</strong> {{ $slipData['receiver']['bank']['name'] ?? '-' }}</li>
        <li><strong>ชื่อบัญชีผู้รับ:</strong> {{ $slipData['receiver']['account']['name'] ?? '-' }}</li>
        <li><strong>เลขบัญชีผู้รับ:</strong> {{ $slipData['receiver']['account']['bank']['account'] ?? '-' }}</li>
        <li><strong>ชื่อผู้โอน:</strong> {{ $slipData['sender']['account']['name'] ?? '-' }}</li>
        <li><strong>ธนาคารผู้โอน:</strong> {{ $slipData['sender']['bank']['name'] ?? '-' }}</li>
        <li><strong>เลขบัญชีผู้โอน:</strong> {{ $slipData['sender']['account']['bank']['account'] ?? '-' }}</li>
    </ul>
</div>
@endif
</div>
