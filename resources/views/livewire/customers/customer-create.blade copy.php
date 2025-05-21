<div>
    {{-- ───────── Toast + Modal Controller (ย้ายมา stack ด้านล่าง) ───────── --}}

    {{-- main card --------------------------------------------------------- --}}
    <br>
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">เพิ่มข้อมูลลูกค้า / ร้านค้า</h4>
                <p class="text-muted mb-0">กรุณากรอกข้อมูลให้ครบถ้วน <code>เพื่อจัดเก็บข้อมูลลูกค้า</code>
                    ให้ถูกต้องสมบูรณ์ที่สุด.</p>

                <button class="btn btn-success float-end" type="submit" wire:loading.attr="disabled" form="saveCustomer">
                    <span wire:loading.remove wire:target="saveCustomer">💾 Save</span>
                    <span wire:loading wire:target="saveCustomer" class="spinner-border spinner-border-sm"></span>
                </button>

            </div>
            <div class="card-body">
                <div class="row">
                    {{-- ---------- NAV ---------- --}}
                    <div class="col-sm-3 mb-2 mb-sm-0">
                        <div class="nav flex-column nav-pills" role="tablist">
                            <a href="#" class="nav-link {{ $activeTab === 'home' ? 'active' : '' }}"
                                wire:click="$set('activeTab','home')">ข้อมูลลูกค้า/ร้านค้า</a>
                            <a href="#" class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}"
                                wire:click="$set('activeTab','profile')">ที่อยู่การจัดส่ง</a>
                            <a href="#" class="nav-link {{ $activeTab === 'wallet' ? 'active' : '' }}"
                                wire:click="$set('activeTab','wallet')">กระเป๋าตัง</a>
                        </div>

                    </div>



                    {{-- ---------- TAB CONTENT ---------- --}}
                    <div class="col-sm-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            {{-- TAB : HOME -------------------------------------------------- --}}
                            <div class="tab-pane fade {{ $activeTab === 'home' ? 'show active' : '' }}"
                                id="v-pills-home">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form wire:submit.prevent="saveCustomer" id="saveCustomer">
                                            {{-- row 1 -------------------------------------------------- --}}
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ประเภทลูกค้า <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" wire:model="customer_type" required>
                                                        <option value="">--กรุณาเลือก--</option>
                                                        @foreach ($customerType as $option)
                                                            <option value="{{ $option }}">{{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ระดับ <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" wire:model="customer_level" required>
                                                        <option value="">--กรุณาเลือก--</option>
                                                        @foreach ($customerLevel as $option)
                                                            <option value="{{ $option }}">{{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- row 2 -------------------------------------------------- --}}
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">เลขประจำตัวผู้เสียภาษี <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_taxid"
                                                        placeholder="เลขประจำตัวผู้เสียภาษี">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ชื่อลูกค้า/ร้านค้า <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_name" placeholder="ชื่อลูกค้า/ร้านค้า"
                                                        required>
                                                </div>
                                            </div>

                                            {{-- row 3 -------------------------------------------------- --}}
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ชื่อผู้ติดต่อ <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_contract_name" placeholder="ชื่อผู้ติดต่อ"
                                                        required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">เบอร์ติดต่อ <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_phone" placeholder="+66" required>
                                                </div>
                                            </div>

                                            {{-- row 4 -------------------------------------------------- --}}
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">อีเมล</label>
                                                    <input type="email" class="form-control"
                                                        wire:model="customer_email" placeholder="email@example.com">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Line id <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_idline" placeholder="@" required>
                                                </div>
                                            </div>

                                            <hr>

                                            {{-- ที่อยู่ใบเสร็จ -------------------------------------------------- --}}
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ที่อยู่ / เลขที่ <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model="customer_address"
                                                        placeholder="เลขที่ / ซอย / ถนน">
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">จังหวัด <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" wire:model.live="customer_province"
                                                        required>
                                                        <option value="">-- เลือกจังหวัด --</option>
                                                        @foreach ($provinces as $code => $name)
                                                            <option value="{{ $code }}">{{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">อำเภอ <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" wire:model.live="customer_amphur"
                                                        @disabled(!$amphures) required>
                                                        <option value="">-- เลือกอำเภอ --</option>
                                                        @foreach ($amphures as $code => $name)
                                                            <option value="{{ $code }}">{{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">ตำบล <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" wire:model.live="customer_district"
                                                        @disabled(!$districts) required>
                                                        <option value="">-- เลือกตำบล --</option>
                                                        @foreach ($districts as $code => $name)
                                                            <option value="{{ $code }}">{{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">รหัสไปรษณีย์ <span class="text-primary"
                                                            style="font-size:11px">*พิมพ์เพื่อค้นหา</span></label>
                                                    <input type="text" class="form-control"
                                                        wire:model.live.debounce.500ms="customer_zipcode"
                                                        maxlength="5" placeholder="รหัสไปรษณีย์" required>
                                                </div>
                                            </div>

                                            {{-- SAVE BUTTON -------------------------------------------------- --}}

                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB : PROFILE -------------------------------------------------- --}}
                            <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}"
                                id="v-pills-profile">
                                <button class="btn btn-sm btn-outline-primary mb-2"
                                    wire:click="$dispatch('open-delivery-modal')">
                                    <i class="fa fa-plus"></i> เพิ่มข้อมูล
                                </button>

                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>ที่อยู่ในการจัดส่ง</th>
                                            <th width="90">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingDeliveries as $idx => $addr)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>
                                                    {{ 'ที่อยู่/เลขที่ : ' . $addr['delivery_number'] . ' ' }}
                                                    {{ 'ตำบล/แขวง : ' . $addr['delivery_district_name'] . ' ' }}
                                                    {{ 'อำเภอ/เขต : ' . $addr['delivery_amphur_name'] . ' ' }}
                                                    {{ 'จังหวัด : ' . $addr['delivery_province_name'] . ' ' }}
                                                    {{ 'รหัสไปรษณีย์ : ' . $addr['delivery_zipcode'] . ' ' }}
                                                    {{ 'ผู้ติดต่อ : ' . $addr['delivery_contact_name'] . ' โทร : ' . $addr['delivery_phone'] }}
                                                </td>
                                                <td class="text-center">
<button class="btn btn-sm btn-warning"
    wire:click="editDeliveryAddress({{ $idx }})">
    ✏️
</button>

                                                    <button class="btn btn-sm btn-danger"
                                                        wire:click="removeTempDelivery({{ $idx }})">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">— ยังไม่มีที่อยู่จัดส่ง —</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
@php
    $editDelivery = isset($pendingDeliveries[$editIndex]) ? $pendingDeliveries[$editIndex] : null;
@endphp

<livewire:customers.delivery-address-modal
    :customer-id="$customer_id"
    :edit-index="$editIndex"
    :edit-delivery="$editDelivery"
    :key="'delivery-address-modal-'.$editIndex.'-'.now()->timestamp"
/>

                            </div>

                            {{-- TAB : WALLET -------------------------------------------------- --}}
                            <div class="tab-pane fade {{ $activeTab === 'wallet' ? 'show active' : '' }}"
                                id="v-pills-wallet">
                                จำเป็นต้องเพิ่มข้อมูลลูกค้าก่อน จึงจะสามารถใช้งานได้
                            </div>
                        </div> <!-- end tab-content -->
                    </div> <!-- end col-sm-9 -->
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================== PUSH SCRIPTS ================== --}}
@push('scripts')
    <script>
        /* ───────── toastr ───────── */
        window.addEventListener('notify', e => {
            const {
                type = 'success', text = ''
            } = e.detail;
            toastr.options = {
                timeOut: 3500,
                progressBar: true,
                positionClass: 'toast-top-right'
            };
            toastr[type](text);
        });

        /* ───────── bootstrap modal helper ───────── */
        document.addEventListener('DOMContentLoaded', () => {
            function safeModal(id, action = 'show') {
                const el = document.getElementById(id);
                if (!el || !window.bootstrap?.Modal) return;
                window.bootstrap.Modal.getOrCreateInstance(el)[action]();
            }
            window.addEventListener('open-delivery-modal', () => safeModal('deliveryModal', 'show'));
            window.addEventListener('close-delivery-modal', () => safeModal('deliveryModal', 'hide'));
        });
    </script>
@endpush
