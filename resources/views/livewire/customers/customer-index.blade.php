<div>
    <br>
    <br>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>รายชื่อลูกค้า</h4>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ เพิ่มลูกค้า</a>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control" placeholder="ค้นหาชื่อลูกค้า / รหัสลูกค้า" wire:model.live.debounce.500ms="search">
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>รหัสลูกค้า</th>
                <th>ชื่อลูกค้า</th>
                <th>ประเภท</th>
                <th>ระดับ</th>
                <th>เบอร์โทร</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $index => $customer)
                <tr>
                    <td>{{ $customers->firstItem() + $index }}</td>
                    <td>{{ $customer->customer_code }}</td>
                    <td>{{ $customer->customer_name }}</td>
                    <td>{{ $customer->customer_type }}</td>
                    <td>{{ $customer->customer_level }}</td>
                    <td>{{ $customer->customer_phone }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $customer->id }})" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">ไม่พบข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $customers->links() }}
    </div>
</div>
