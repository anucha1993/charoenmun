<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\customers\customerModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public $totalCustomers;
    public $newCustomers;
    public $inactiveCustomers;

    protected $listeners = ['deleteCustomer'];

    public function mount()
    {
        $this->loadCustomerStats();
    }

    public function loadCustomerStats()
    {
        // จำนวนลูกค้าทั้งหมด
        $this->totalCustomers = customerModel::count();

        // ลูกค้าใหม่ (3 เดือนล่าสุด)
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $this->newCustomers = customerModel::where('created_at', '>=', $threeMonthsAgo)->count();

        // ลูกค้าที่ไม่มีการเคลื่อนไหว (ไม่มีการสั่งซื้อใน 6 เดือน)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $this->inactiveCustomers = customerModel::whereNotExists(function ($query) use ($sixMonthsAgo) {
            $query->select(DB::raw(1))
                  ->from('orders')
                  ->whereRaw('orders.customer_id = customers.id')
                  ->where('orders.created_at', '>=', $sixMonthsAgo);
        })->count();
    }


 public function __construct(array $data = [])
    {
        // ไม่ต้องทำอะไรกับ $data ก็ได้
    }

    public function updatingSearch() { $this->resetPage(); }

    public function deleteCustomer(int $id): void
    {
        if ($id) {
            customerModel::findOrFail($id)->delete();
            $this->dispatch('notify', message: 'ลบลูกค้าเรียบร้อยแล้ว');
        }
    }

    public function export($type = 'all')
    {
        $typeNames = [
            'all' => 'ทั้งหมด',
            'new' => 'รายใหม่',
            'inactive' => 'ไม่มีการเคลื่อนไหว'
        ];

        $filename = 'customers-' . $typeNames[$type] . '-' . now()->format('Y-m-d-His') . '.xlsx';
        $this->dispatch('notify', message: 'กำลังดาวน์โหลดไฟล์ Excel...');
        return Excel::download(new CustomersExport($this->search, $type), $filename);
    }    public function render()
    {
        $customers = customerModel::query()
            ->when($this->search, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('customer_name',  'like', "%{$this->search}%")
                      ->orWhere('customer_phone', 'like', "%{$this->search}%")
                      ->orWhere('customer_email', 'like', "%{$this->search}%");
                })
            )
            ->latest('id')
            ->paginate(15);

        return view('livewire.customers.customer-index', compact('customers'))
               ->layout('layouts.horizontal', ['title' => 'Customers - List']);
    }
}
