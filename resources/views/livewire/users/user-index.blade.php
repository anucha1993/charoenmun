<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">จัดการผู้ใช้งาน</h4>
                            <p class="text-muted mb-0">รายการผู้ใช้งานทั้งหมดในระบบ</p>
                        </div>
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="ri-add-circle-line"></i> เพิ่มผู้ใช้งาน
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input wire:model.live="search" type="search" class="form-control" placeholder="ค้นหาผู้ใช้งาน...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>ชื่อ</th>
                                    <th>อีเมล</th>
                                    <th>ประเภท</th>
                                    <th>วันที่สร้าง</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->type === 'SA')
                                                <span class="badge bg-success">ผู้ดูแลระบบ</span>
                                            @elseif($user->type === 'admin')
                                                <span class="badge bg-primary">แอดมิน</span>
                                            @else
                                                <span class="badge bg-info">สมาชิก</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button wire:click="delete({{ $user->id }})" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirm('ยืนยันการลบผู้ใช้งานนี้?') || event.stopImmediatePropagation()">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
