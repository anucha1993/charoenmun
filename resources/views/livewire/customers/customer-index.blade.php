<div>
    <br>

<div class="row">
        <div class="col-md-4">
            <div class="card border-secondary border">
                <div class="card-body">
                    <h5 class="card-title">จำนวนลูกค้าทั้งหมด</h5>
                    <h2 class="my-2">{{$customers->count()}} ราย</h2>
                    <p class="card-text">นับจำนวนจากข้อมูลลูกค้าทั้งหมด</p>
                    
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-md-4">
            <div class="card border-primary border">
                <div class="card-body">
                    <h5 class="card-title text-primary">ลูกค้ารายใหม่</h5>
                     <h2 class="my-2">{{$customers->count()}} ราย</h2>
                    <p class="card-text">คิดจากลูกค้าที่เพิ่มในระบบในระยะเวลา 3 เดือน</p>
                  
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-md-4">
            <div class="card border-success border">
                <div class="card-body">
                    <h5 class="card-title text-success">ลูกค้าที่ไม่การเคลือนไหว</h5>
                        <h2 class="my-2">{{$customers->count()}} ราย</h2>
                    <p class="card-text">คิดจากลูกค้าที่ไม่มีการสั่งซื้อสินค้าเป็นระยะยเวลานาน</p>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row -->

     <div class="card">
        <div class="card-header">
            <h4>รายการข้อมูลลูกค้าทั้งหมด</h4>
            <a href="{{route('customers.create')}}" class="float-end btn btn-outline-success" style="font-size: 18px">+ เพิ่มข้อมูลลูกค้า</a>
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
                    <th>ชื่อผู้ติดต่อ</th>
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
                        <td>{{ $customer->customer_contract_name }}</td>
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
