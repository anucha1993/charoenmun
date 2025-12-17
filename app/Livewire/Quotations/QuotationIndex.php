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
    public ?string $filterStatus = null; // สำหรับกรองตามเมนูที่เลือก

    // สอง public properties สำหรับเก็บข้อมูลกราฟ
    public array $statusData   = [];
    public array $customerData = [];

    protected array $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount()
    {
        // ตรวจสอบ route name เพื่อกำหนดสถานะ
        $routeName = request()->route()->getName();
        
        if ($routeName === 'quotations.pending') {
            $this->filterStatus = 'wait';
            $this->status = 'wait';
        } elseif ($routeName === 'quotations.approved') {
            $this->filterStatus = 'success';
            $this->status = 'success';
        } elseif ($routeName === 'quotations.cancelled') {
            $this->filterStatus = 'cancel';
            $this->status = 'cancel';
        }
        
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

    public function refreshData()
    {
        $this->resetPage();
         $this->search = '';
        $this->status = '';
        $this->computeChartData();
        $this->dispatch('chart-update', [
            'status'    => $this->statusData,
            'customers' => $this->customerData,
        ]);
    }

    private function computeChartData()
    {
        // Query สำหรับ status summary (filter ด้วย search และ status)
        $queryForStatus = QuotationModel::query()
            ->when($this->search, function($q) {
                $q->where(function($q) {
                    $q->where('quote_number','like', "%{$this->search}%")
                      ->orWhereHas('customer', fn($c) =>
                          $c->where('customer_name','like', "%{$this->search}%")
                      );
                });
            })
            ->when($this->status, fn($q) =>
                $q->where('quote_status', $this->status)
            );

        // 1) นับใบเสนอราคาแยกตามสถานะ (filter สถานะด้วย)
        $statusCounts = (clone $queryForStatus)
            ->select('quote_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('quote_status')
            ->get()
            ->mapWithKeys(function($item) {
                $status = $item->getRawOriginal('quote_status');
                return [$status => $item->total];
            });

        $this->statusData = [
            'labels' => ['รอดำเนินการ','ยืนยันแล้ว','ยกเลิก'],
            'data'   => [
                $statusCounts['wait']    ?? 0,
                $statusCounts['success'] ?? 0,
                $statusCounts['cancel']  ?? 0,
            ],
        ];

        // 2) Top 10 ลูกค้า (filter ด้วย search และ status ตามปกติ)
        $queryForGraph = QuotationModel::query()
            ->when($this->search, function($q) {
                $q->where(function($q) {
                    $q->where('quote_number','like', "%{$this->search}%")
                      ->orWhereHas('customer', fn($c) =>
                          $c->where('customer_name','like', "%{$this->search}%")
                      );
                });
            })
            ->when($this->status, fn($q) =>
                $q->where('quote_status', $this->status)
            );

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
            ->when($this->search, function($q) {
                $q->where(function($q) {
                    $q->where('quote_number','like', "%{$this->search}%")
                      ->orWhereHas('customer', fn($c) =>
                          $c->where('customer_name','like', "%{$this->search}%")
                      );
                });
            })
            ->when($this->status, fn($q) =>
                $q->where('quote_status', $this->status)
            )
            ->latest()
            ->paginate(15);

        $statuses = ['wait','success','cancel'];
        
        // กำหนด view ตามสถานะที่เลือก
        $viewName = 'livewire.quotations.quotation-index';
        if ($this->filterStatus === 'wait') {
            $viewName = 'livewire.quotations.quotation-index-pending';
        } elseif ($this->filterStatus === 'success') {
            $viewName = 'livewire.quotations.quotation-index-approved';
        } elseif ($this->filterStatus === 'cancel') {
            $viewName = 'livewire.quotations.quotation-index-cancelled';
        }

        return view($viewName, compact('quotes','statuses'))
               ->layout('layouts.horizontal', ['title' => 'Quotations']);
    }
}
