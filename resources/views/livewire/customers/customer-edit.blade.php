<div>
    <form wire:submit.prevent="update" class="space-y-4">
        <div>
            <label>ชื่อลูกค้า</label>
            <input type="text" wire:model="customer_name" class="form-control">
        </div>

        <hr>

        <div class="flex justify-between items-center">
            <h4>ที่อยู่จัดส่ง</h4>
            <button type="button" wire:click="openDeliveryModal" class="btn btn-success">+ เพิ่มที่อยู่</button>
        </div>

        <ul>
            @foreach($deliveryAddresses as $index => $address)
                <li class="border p-2 mb-2">
                    <div>เลขที่: {{ $address['delivery_number'] ?? '-' }}</div>
                    <div>จังหวัด: {{ $address['delivery_province'] ?? '-' }}</div>
                    <button type="button" wire:click="openDeliveryModal({{ $index }})">แก้ไข</button>
                    <button type="button" wire:click="removeDelivery({{ $index }})" class="text-red-500">ลบ</button>
                </li>
            @endforeach
        </ul>

        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>

    {{-- Modal --}}
    @if($showDeliveryModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-4 w-96 rounded shadow">
            <h4>ที่อยู่จัดส่ง</h4>

            <label>เลขที่</label>
            <input type="text" wire:model="deliveryForm.delivery_number" class="form-control">

            <label>จังหวัด</label>
            <input type="text" wire:model="deliveryForm.delivery_province" class="form-control">

            <label>เขต/อำเภอ</label>
            <input type="text" wire:model="deliveryForm.delivery_amphur" class="form-control">

            <label>แขวง/ตำบล</label>
            <input type="text" wire:model="deliveryForm.delivery_district" class="form-control">

            <label>รหัสไปรษณีย์</label>
            <input type="text" wire:model="deliveryForm.delivery_zipcode" class="form-control">

            <label>ชื่อผู้ติดต่อ</label>
            <input type="text" wire:model="deliveryForm.delivery_contact_name" class="form-control">

            <label>เบอร์โทร</label>
            <input type="text" wire:model="deliveryForm.delivery_phone" class="form-control">

            <div class="mt-4">
                <button type="button" wire:click="saveDelivery" class="btn btn-primary">บันทึก</button>
                <button type="button" wire:click="$set('showDeliveryModal', false)" class="btn btn-secondary">ยกเลิก</button>
            </div>
        </div>
    </div>
    @endif
</div>
