<div>
    <br>
     <div class="card">
        <div class="card-header">
            <h4>รายการข้อมูลลูกค้าทั้งหมด</h4>
        </div>
        <div class="card-body">

    {{-- Search --}}
    <div class="mb-3">
        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="ค้นหา ชื่อ, เบอร์โทร, อีเมล...">
    </div>

    {{-- Table --}}




    <div class="table-responsive">
        <table class="table table">
            <thead class="table-dark">
                <tr>
                    <th>รหัสลูกค้า</th>
                    <th>ชื่อลูกค้า</th>
                    <th>เบอร์โทร</th>
                    <th>ประเภท</th>
                    <th>ระดับ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->customer_code }}</td>
                        <td>{{ $customer->customer_name }}</td>
                        <td>{{ $customer->customer_phone }}</td>
                        <td>
                              <span class="badge bg-primary">{{ $customer->type->value }}</span>
                        </td>
                        <td>
                           <span class="badge bg-success">{{ $customer->level->value }}</span>
                        </td>
                        <td>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-info"> <i class="mdi mdi-grease-pencil"></i> แก้ไข</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $customer->id }})">  <i class="mdi mdi-trash-can"></i> ลบ</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">ไม่พบข้อมูล</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div>
          {{ $customers->links( "pagination::bootstrap-5") }}

    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('คุณต้องการลบลูกค้ารายนี้หรือไม่?')) {
                Livewire.dispatch('deleteCustomer', { id: id });
            }
        }
    </script>
</div>
