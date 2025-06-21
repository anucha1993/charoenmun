<div>
    <!-- Modal -->
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="bs-example-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                          @if ($editing)
                                แก้ไขที่อยู่จัดส่ง
                            @else
                               เพิ่มที่อยู่จัดส่ง
                            @endif
                        
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- 🧾 แสดงข้อมูลที่โหลดจาก customer --}}
                    <div class="mb-3">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <input type="text" class="form-control" wire:model.defer="customer_name">
                    </div>


                    <form wire:submit.prevent="{{ $editing ? 'updateDelivery' : 'saveDelivery' }}">

                        <div class="mb-2">
                            <label for="username" class="form-label">ชื่อผู้ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_contact_name" type="text"
                                placeholder="ชื่อผู้ติดต่อ">
                        </div>
                        <div class="mb-2">
                            <label for="username" class="form-label">เบอร์ติดต่อ</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_phone" type="text"
                                placeholder="เบอร์ติดต่อ">
                        </div>

                        <div class="mb-2">
                            <label for="username" class="form-label">เลขที่/หมู่/ซอย</label>
                            <input class="form-control" wire:model="deliveryForm.delivery_number" type="text"
                                placeholder="เลขที่/หมู่/ซอย">
                        </div>

                        <!-- จังหวัด -->
                        <div class="mb-2">
                            <label for="">จังหวัด</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_province" >
                                <option value="">-- เลือกจังหวัด --</option>
                                @foreach ($deliveryProvinces as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- อำเภอ -->
                        <div class="mb-2">
                            <label for="">อำเภอ/เขต</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_amphur"
                                @disabled(!$deliveryAmphures) >
                                <option value="">-- เลือกอำเภอ --</option>
                                @foreach ($deliveryAmphures as $code => $name)
                                    <option value="{{ $code }}"
                                        @if ($deliveryForm['delivery_amphur'] == $code) selected @endif>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ตำบล -->
                        <div class="mb-2">
                            <label for="">ตำบล/แขวง</label>
                            <select class="form-select" wire:model.live="deliveryForm.delivery_district"
                                @disabled(!$deliveryDistricts) >
                                <option value="">-- เลือกตำบล --</option>

                                @foreach ($deliveryDistricts as $code => $name)
                                    <option value="{{ $code }}"
                                        @if ($deliveryForm['delivery_district'] == $code) selected @endif>
                                        {{ $name }}
                                    </option>
                                @endforeach


                            </select>
                        </div>

                        <!-- รหัสไปรษณีย์ -->
                        <div class="mb-2">
                            <label for="">รหัสไปรษณีย์ <span class="text-primary"
                                    style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                            <input type="text" wire:model.live.debounce.500ms="deliveryForm.delivery_zipcode"
                                class="form-control">
                        </div>

                        {{-- เพิ่ม field ได้ตามต้องการ --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal"
                                data-bs-dismiss="modal">ปิด</button>
                            @if ($editing)
                                <button type="submit" class="btn btn-warning">อัปเดต</button>
                            @else
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            @endif
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


</div>
