<div>
    {{-- ───── Toast + Modal Controller ───── --}}
    <script>
        /* toastr */
        window.addEventListener('notify', e => {
            const { type='success', text='' } = e.detail
            toastr.options = { timeOut: 3500, progressBar: true, positionClass: 'toast-top-right' }
            toastr[type](text)
        })

        /* bootstrap modal */
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('global-set-modal'))
            window.addEventListener('open-modal',  () => modal.show())
            window.addEventListener('close-modal', () => modal.hide())
        })
    </script>
<div class="container-fluid">
    {{-- ───── Page Header ───── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 page-title-box ">
        <h3 class="page-title">รายชื่อลูกค้า</h3>
        <button class="btn btn-primary" wire:click="create">
            <i class="ri-add-line me-1"></i> เพิ่ม
        </button>
    </div>
   

    {{-- ───── Card & Table ───── --}}
    <div class="card shadow-sm  ">
        <div class="card-header"><h3 class="mb-0 ">รายชื่อลูกค้าทั้งหมด</h3></div>
    </div>
</div>