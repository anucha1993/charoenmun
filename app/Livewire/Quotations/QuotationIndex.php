<?php
namespace App\Livewire\Quotations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quotations\QuotationModel;

class QuotationIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    // สอง public properties สำหรับเก็บข้อมูลกราฟ
    public array $statusData   = [];
    public array $customerData = [];

    protected array $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount()
    {
        // คำนวณข้อมูลกราฟครั้งแรก (initial load)
        $this->computeChartData();
        // ** ไม่ต้อง dispatch ตรงนี้อีก เพราะเราจะฝัง $statusData/$customerData ลง Blade เลย **
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->computeChartData();
        $this->dispatch('chart-update', [
            'status'    => $this->statusData,
            'customers' => $this->customerData,
        ]);
    }

    public function updatedStatus()
    {
        $this->resetPage();
        $this->computeChartData();
        $this->dispatch('chart-update', [
            'status'    => $this->statusData,
            'customers' => $this->customerData,
        ]);
    }

    public function delete(int $id)
    {
        QuotationModel::findOrFail($id)->delete();
        $this->dispatch('notify', [
            'type' => 'success',
            'text' => 'ลบใบเสนอราคาแล้ว'
        ]);
        $this->computeChartData();
        $this->dispatch('chart-update', [
            'status'    => $this->statusData,
            'customers' => $this->customerData,
        ]);
    }

    private function computeChartData()
    {
        // สร้าง query พื้นฐานสำหรับกราฟ (filter search/status)
        $queryForGraph = QuotationModel::query()
            ->when($this->search, fn($q) =>
                $q->where('quote_number','like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($c) =>
                      $c->where('customer_name','like', "%{$this->search}%")
                  )
            )
            ->when($this->status, fn($q) =>
                $q->where('quote_status', $this->status)
            );

        // 1) นับใบเสนอราคาแยกตามสถานะ
        $statusCounts = (clone $queryForGraph)
            ->select('quote_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('quote_status')
            ->pluck('total','quote_status');

        $this->statusData = [
            'labels' => ['รอดำเนินการ','ยืนยันแล้ว','ยกเลิก'],
            'data'   => [
                $statusCounts['wait']    ?? 0,
                $statusCounts['success'] ?? 0,
                $statusCounts['cancel']  ?? 0,
            ],
        ];

        // 2) Top 10 ลูกค้า (โดยจำกัด 10 รายเรียงตามจำนวนใบเสนอราคา)
        $topCustomers = (clone $queryForGraph)
            ->selectRaw('customer_id, COUNT(*) as total')
            ->groupBy('customer_id')
            ->orderByDesc('total')
            ->with('customer:id,customer_name')
            ->take(10)
            ->get();

        $this->customerData = [
            'labels' => $topCustomers->pluck('customer.customer_name')->toArray(),
            'data'   => $topCustomers->pluck('total')->toArray(),
        ];
    }

    public function render()
    {
        $quotes = QuotationModel::with(['customer','sale'])
            ->when($this->search, fn($q) =>
                $q->where('quote_number','like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($c) =>
                      $c->where('customer_name','like', "%{$this->search}%")
                  )
            )
            ->when($this->status, fn($q) =>
                $q->where('quote_status', $this->status)
            )
            ->latest()
            ->paginate(15);

        $statuses = ['wait','success','cancel'];

        return view('livewire.quotations.quotation-index', compact('quotes','statuses'))
               ->layout('layouts.horizontal', ['title' => 'Quotations']);
    }
}
