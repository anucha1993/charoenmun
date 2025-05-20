<div>
    {{-- ───── Toast + Modal Controller ───── --}}
    <script>
        /* toastr */
        window.addEventListener('notify', e => {
            const {
                type = 'success', text = ''
            } = e.detail
            toastr.options = {
                timeOut: 3500,
                progressBar: true,
                positionClass: 'toast-top-right'
            }
            toastr[type](text)
        })

        /* bootstrap modal */
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('global-set-modal'))
            window.addEventListener('open-modal', () => modal.show())
            window.addEventListener('close-modal', () => modal.hide())
        })
    </script>
    <div class="container-fluid">
        <br>
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">เพิ่มข้อมูลลูกค้า / ร้านค้า</h4>
                        <p class="text-muted mb-0">
                            กรุณากรอกข้อมูลให้ตรบถ้วน <code>เพื่อจัดเก็บข้อมูลลูกค้า</code> ให้ถูกต้องสมบูรณ์ที่สุด.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 mb-2 mb-sm-0">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link show active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true" tabindex="-1">
                                        ข้อมูลลูกค้า/ร้านค้า
                                    </a>
                                    <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false" tabindex="-1">
                                        ที่อยู่การจัดส่ง
                                    </a>
                                    <a class="nav-link " id="v-pills-settings-tab" data-bs-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                        กระเป๋าตัง
                                    </a>
                                </div>
                            </div> <!-- end col-->
    
                            <div class="col-sm-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <form>
                                                    <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ประเภทลูกค้า <span class="text-danger"> *</span></label>
                                                        <select class="form-select" id="">
                                                            <option value="">--กรุณาเลือก--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ระดับ <span class="text-danger"> *</span></label>
                                                        <select class="form-select" id="">
                                                            <option value="">--กรุณาเลือก--</option>
                                                        </select>
                                                    </div>
                                                   
                                                 </div>
                                                 <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">เลขประจำตัวผู้เสียภาษี <span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="เลขประจำตัวผู้เสียภาษี">
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ชื่อลูกค้า/ร้านค้า <span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="ชื่อลูกค้า/ร้านค้า" required>
                                                    </div>
                                                 </div>
                        
                                                 <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ชื่อผู้ติดต่อ<span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="ชื่อผู้ติดต่อ" required>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">เบอร์ติดต่อ<span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="++66" required>
                                                    </div>
                                                 </div>
                        
                                                 <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">อีเมล</label>
                                                        <input type="email" id="simpleinput" class="form-control" placeholder="email@gmail.com">
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">Line id<span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="@">
                                                    </div>
                                                 </div>
                                                 <hr>
                        
                                                 <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ที่อยู่ / เลขที่ <span class="text-danger">*</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="ชื่อผู้ติดต่อ">
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">จังหวัด<span class="text-danger"> *</span></label>
                                                        <select class="form-select" id="">
                                                            <option value="">--กรุณาเลือก--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">อำเภอ<span class="text-danger"> *</span></label>
                                                        <select class="form-select" id="">
                                                            <option value="">--กรุณาเลือก--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">ตำบล<span class="text-danger"> *</span></label>
                                                        <select class="form-select" id="">
                                                            <option value="">--กรุณาเลือก--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label for="simpleinput" class="form-label">รหัสไปรษณีย์<span class="text-danger"> *</span></label>
                                                        <input type="text" id="simpleinput" class="form-control" placeholder="รหัสไปรษณีย์" readonly>
                                                    </div>
                                                 </div>
                                                    
                        
                                                </form>
                                            </div> <!-- end col -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                        <p class="mb-0">Culpa dolor voluptate do laboris laboris irure reprehenderit id incididunt duis pariatur mollit aute magna
                                            pariatur consectetur. Eu veniam duis non ut dolor deserunt commodo et minim in quis laboris ipsum velit
                                            id veniam. Quis ut consectetur adipisicing officia excepteur non sit. Ut et elit aliquip labore Leggings
                                            enim eu. Ullamco mollit occaecat dolore ipsum id officia mollit qui esse anim eiusmod do sint minim consectetur
                                            qui.</p>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                        <p class="mb-0">Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis
                                            natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque
                                            eu, pretium quis, sem. Nulla consequat massa quis enim. Cillum ad ut irure tempor velit nostrud occaecat ullamco
                                            aliqua anim Leggings sint. Veniam sint duis incididunt do esse magna mollit excepteur laborum qui.</p>
                                    </div>
                                </div> <!-- end tab-content-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row--> 
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div>


        </div>

  
