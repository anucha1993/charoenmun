<?php

namespace App\Livewire\Orders;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderDeliverysModel;

class ScanInvoice extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    
    protected $queryString = [
        'filterType' => ['except' => 'today'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'deliveryStatus' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public $scanInput = '';
    public ?OrderDeliverysModel $currentDelivery = null;
    public ?OrderModel $order = null;
    public array $deliveredQtyMap = [];
    public $todayApprovedDeliveries = [];
    
    // Filter properties
    public $filterType = 'today'; // today, date-range, pending, all
    public $startDate;
    public $endDate;
    public $deliveryStatus = ''; // '', 'success', 'pending', 'cancelled'
    
    // Stats
    public $stats = [
        'today' => ['count' => 0, 'amount' => 0],
        'pending' => ['count' => 0, 'amount' => 0],
        'success' => ['count' => 0, 'amount' => 0],
        'monthly' => ['count' => 0, 'amount' => 0],
        'yearly' => ['count' => 0, 'amount' => 0],
    ];

    protected function resetScanData()
    {
        $this->currentDelivery = null;
        $this->order = null;
        $this->deliveredQtyMap = [];
    }

    public function mount()
    {
        // กำหนดค่าเริ่มต้น
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->filterType = 'today';  // เริ่มต้นด้วยรายการวันนี้
        $this->deliveryStatus = '';   // เริ่มต้นแสดงทุกสถานะ
        $this->resetScanData();
        $this->loadStats();
    }

    public function updatedScanInput()
    {
        if (empty($this->scanInput)) {
            $this->resetScanData();
            return;
        }

        $delivery = OrderDeliverysModel::with([
                'order.customer',
                'order.items',
                'order.deliveryAddress',
                'order.deliveries.deliveryItems'
            ])
            ->where('order_delivery_number', $this->scanInput)
            ->first();

        if ($delivery) {
            // Mark as success
            $delivery->order_delivery_status = 'success';
            $delivery->order_delivery_status_date = now();
            $delivery->save();

            $this->currentDelivery = $delivery;
            $this->order = $delivery->order;

            // Update deliveredQtyMap only if we have data
            if ($this->order) {
                $this->deliveredQtyMap = OrderDeliverysModel::where('order_id', $this->order->id)
                    ->with('deliveryItems')
                    ->get()
                    ->flatMap(fn($delivery) => $delivery->deliveryItems)
                    ->groupBy('product_id')
                    ->map(fn($group) => $group->sum('quantity'))
                    ->toArray();
            }

            $this->dispatch('notify', type: 'success', message: 'ตรวจสอบการจัดส่งสำเร็จ ✅');
        } else {
            $this->resetScanData();
            $this->dispatch('notify', type: 'error', message: 'ไม่พบบิลย่อยนี้ ❌');
        }

        $this->scanInput = '';
        $this->loadStats();
    }

    public function setFilter($type)
    {
        // รีเซ็ตตัวกรองเก่า
        $this->reset(['deliveryStatus']);
        
        // กำหนดประเภทตัวกรองใหม่
        $this->filterType = $type;
        
        // ถ้าเป็นช่วงวันที่ ให้กำหนดค่าเริ่มต้น
        if ($type === 'date-range') {
            $this->startDate = now()->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        }

        $this->resetPage();
        $this->dispatch('filter-changed');  // ส่ง event แจ้งว่ามีการเปลี่ยนตัวกรอง
    }

    public function updatedDeliveryStatus($value)
    {
        $this->resetPage();
        $this->dispatch('refresh-list');
    }

    public function updatedStartDate($value)
    {
        if ($this->filterType === 'date-range') {
            $this->validateDates();
            $this->resetPage();
            $this->dispatch('refresh-list');
        }
    }

    public function updatedEndDate($value)
    {
        if ($this->filterType === 'date-range') {
            $this->validateDates();
            $this->resetPage();
            $this->dispatch('refresh-list');
        }
    }

    protected function validateDates()
    {
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            
            if ($end->lt($start)) {
                $this->endDate = $this->startDate;
            }
        }
    }

    public function cancelSuccess($deliveryId)
    {
        $delivery = OrderDeliverysModel::find($deliveryId);
        if ($delivery) {
            $delivery->order_delivery_status = 'pending';
            $delivery->order_delivery_status_date = null;
            $delivery->save();
            $this->loadStats();
            $this->dispatch('notify', type: 'success', message: 'ยกเลิกการตรวจสอบสำเร็จ');

            // ถ้ายกเลิกบิลที่กำลังดูอยู่ ให้ clear current delivery
            if ($this->currentDelivery && $this->currentDelivery->id === $deliveryId) {
                $this->resetScanData();
            }
        }
    }

    private function loadStats()
    {
        // Load today's approved deliveries
        $this->todayApprovedDeliveries = OrderDeliverysModel::with(['order.customer'])
            ->whereDate('order_delivery_status_date', today())
            ->where('order_delivery_status', 'success')
            ->latest('order_delivery_status_date')
            ->get();

        // Today's stats
        $this->stats['today'] = [
            'count' => OrderDeliverysModel::whereDate('order_delivery_date', today())->count(),
            'amount' => OrderDeliverysModel::whereDate('order_delivery_date', today())->sum('order_delivery_grand_total')
        ];

        // Pending stats (delivery date < today && not success)
        $this->stats['pending'] = [
            'count' => OrderDeliverysModel::where('order_delivery_date', '<', today())
                ->where('order_delivery_status', '!=', 'success')
                ->count(),
            'amount' => OrderDeliverysModel::where('order_delivery_date', '<', today())
                ->where('order_delivery_status', '!=', 'success')
                ->sum('order_delivery_grand_total')
        ];

        // Success stats
        $this->stats['success'] = [
            'count' => OrderDeliverysModel::where('order_delivery_status', 'success')->count(),
            'amount' => OrderDeliverysModel::where('order_delivery_status', 'success')->sum('order_delivery_grand_total')
        ];

        // Monthly stats (current month)
        $this->stats['monthly'] = [
            'count' => OrderDeliverysModel::whereYear('order_delivery_date', now()->year)
                ->whereMonth('order_delivery_date', now()->month)
                ->count(),
            'amount' => OrderDeliverysModel::whereYear('order_delivery_date', now()->year)
                ->whereMonth('order_delivery_date', now()->month)
                ->sum('order_delivery_grand_total')
        ];

        // Yearly stats
        $this->stats['yearly'] = [
            'count' => OrderDeliverysModel::whereYear('order_delivery_date', now()->year)
                ->count(),
            'amount' => OrderDeliverysModel::whereYear('order_delivery_date', now()->year)
                ->sum('order_delivery_grand_total')
        ];
    }

    public function getDeliveriesProperty()
    {
        $query = OrderDeliverysModel::query()
            ->with(['order.customer']);

        // กรองตามประเภท
        switch ($this->filterType) {
            case 'today':
                $today = now()->format('Y-m-d');
                $query->whereRaw("DATE(order_delivery_date) = ?", [$today]);
                break;
            case 'pending':
                // รายการที่ยังไม่เสร็จสิ้น (pending หรือ processing)
                $query->whereIn('order_delivery_status', ['pending', 'processing']);
                break;
            case 'date-range':
                if ($this->startDate && $this->endDate) {
                    $start = Carbon::parse($this->startDate)->startOfDay();
                    $end = Carbon::parse($this->endDate)->endOfDay();
                    $query->whereBetween('order_delivery_date', [$start, $end]);
                }
                break;
        }

        // กรองตามสถานะ
        if ($this->deliveryStatus !== '') {
            $query->where('order_delivery_status', $this->deliveryStatus);
        }

        // Debug: แสดง SQL query
        \Log::info('Delivery Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'filter_type' => $this->filterType,
            'delivery_status' => $this->deliveryStatus,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);

        // เรียงลำดับและแบ่งหน้า
        return $query->latest('order_delivery_date')
                    ->paginate(10);
    }

    public function getPendingDeliveriesProperty()
    {
        return OrderDeliverysModel::with(['order.customer'])
            ->where('order_delivery_date', '<', today())
            ->where('order_delivery_status', '!=', 'success')
            ->latest('order_delivery_date')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.orders.scan-invoice', [
            'deliveries' => $this->deliveries,
            'pendingDeliveries' => $this->pendingDeliveries,
            'title' => 'สแกนตรวจสอบการจัดส่ง'
        ]);
    }
}

