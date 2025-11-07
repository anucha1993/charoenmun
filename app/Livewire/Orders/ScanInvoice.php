<?php

namespace App\Livewire\Orders;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderPayment;
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
    public $scannedDeliveryPayments = [];
    public $todayApprovedDeliveries = [];
    public $allPendingPayments = [];
    
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
        $this->scannedDeliveryPayments = [];
        $this->loadStats(); // Refresh รายการวันนี้และ pending payments
    }

    /**
     * แยกหมายเลขใบส่งของจาก QR Code ที่อาจมีรูปแบบต่างๆ
     * รองรับทั้ง: "OR25110036-001" และ "Delivery ID: 131 - OR25110036-001"
     */
    public function extractDeliveryNumber($input)
    {
        $input = trim($input);
        
        // ถ้าเป็นรูปแบบ "Delivery ID: 131 - OR25110036-001"
        if (preg_match('/Delivery ID:\s*\d+\s*-\s*(.+)/', $input, $matches)) {
            return trim($matches[1]);
        }
        
        // ถ้าเป็นรูปแบบ "OR25110036-001" หรือรูปแบบอื่นๆ
        return $input;
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

        // แยกหมายเลขใบส่งของจาก QR Code ที่อาจมีรูปแบบ "Delivery ID: 131 - OR25110036-001"
        $deliveryNumber = $this->extractDeliveryNumber($this->scanInput);
        
        // Log สำหรับ debug - เพิ่มข้อมูลมากขึ้น
        Log::info('QR Code Scan Debug:', [
            'step' => 'input_received',
            'original_input' => $this->scanInput,
            'extracted_number' => $deliveryNumber,
            'input_length' => strlen($this->scanInput),
            'extracted_length' => strlen($deliveryNumber),
            'has_special_chars' => preg_match('/[^\w\-]/', $deliveryNumber) ? 'yes' : 'no'
        ]);

        $delivery = OrderDeliverysModel::with([
                'order.customer',
                'order.items',
                'order.deliveryAddress',
                'order.deliveries.deliveryItems'
            ])
            ->where('order_delivery_number', $deliveryNumber)
            ->first();
            
        // Log ผลการค้นหา
        Log::info('QR Code Search Result:', [
            'step' => 'database_search',
            'search_term' => $deliveryNumber,
            'found' => $delivery ? 'yes' : 'no',
            'delivery_id' => $delivery ? $delivery->id : null,
            'status' => $delivery ? $delivery->order_delivery_status : null
        ]);

        if ($delivery) {
            // Mark as success
            $delivery->order_delivery_status = 'success';
            $delivery->order_delivery_status_date = now();
            $delivery->save();

            // โหลดสลิปรอยืนยันของ order นี้
            $this->loadScannedDeliveryPayments($delivery->order_id);
            
            // Refresh stats และรายการวันนี้
            $this->loadStats();

            $this->dispatch('notify', type: 'success', message: 'ยืนยันการจัดส่งสำเร็จ ✅');
        } else {
            $this->resetScanData();
            $this->dispatch('notify', type: 'error', message: 'ไม่พบบิลย่อยนี้ ❌');
        }

        $this->scanInput = '';
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
        // ตรวจสอบสิทธิ์ในการยกเลิกการยืนยันการจัดส่ง
        if (!auth()->user()->canConfirmDelivery()) {
            session()->flash('error', 'คุณไม่มีสิทธิ์ในการยกเลิกการยืนยันการจัดส่ง');
            $this->dispatch('notify', type: 'error', message: 'คุณไม่มีสิทธิ์ในการยกเลิกการยืนยันการจัดส่ง');
            return;
        }

        $delivery = OrderDeliverysModel::find($deliveryId);
        if ($delivery) {
            $delivery->order_delivery_status = 'pending';
            $delivery->order_delivery_status_date = null;
            $delivery->save();
            $this->loadStats();
            
            // ถ้ายกเลิกบิลที่กำลังดูอยู่ ให้ clear ข้อมูล payments ด้วย
            if ($this->currentDelivery && $this->currentDelivery->id === $deliveryId) {
                $this->resetScanData();
            }
            $this->dispatch('notify', type: 'success', message: 'ยกเลิกการยืนยันสำเร็จ');

            // ถ้ายกเลิกบิลที่กำลังดูอยู่ ให้ clear current delivery
            if ($this->currentDelivery && $this->currentDelivery->id === $deliveryId) {
                $this->resetScanData();
            }
        }
    }

    public function confirmPayment($paymentId)
    {
        // ตรวจสอบสิทธิ์ในการอนุมัติการชำระเงิน
        if (!auth()->user()->canApprovePayment()) {
            session()->flash('error', 'คุณไม่มีสิทธิ์ในการอนุมัติการชำระเงิน');
            $this->dispatch('notify', type: 'error', message: 'คุณไม่มีสิทธิ์ในการอนุมัติการชำระเงิน');
            return;
        }

        $payment = OrderPayment::find($paymentId);
        if ($payment) {
            $payment->status = 'ชำระเงินแล้ว';
            $payment->save();
            
            // อัปเดตสถานะการชำระเงินของออเดอร์
            $order = $payment->order;
            $order->updatePaymentStatus();
            
            // Refresh payments list
            $this->refreshPayments();
            
            session()->flash('success', 'ยืนยันสลิปเรียบร้อยแล้ว ✅');
            $this->dispatch('notify', type: 'success', message: 'ยืนยันสลิปเรียบร้อยแล้ว ✅');
        }
    }

    public function rejectWithReason($paymentId, $reason)
    {
        // ตรวจสอบสิทธิ์ในการปฏิเสธการชำระเงิน
        if (!auth()->user()->canApprovePayment()) {
            session()->flash('error', 'คุณไม่มีสิทธิ์ในการปฏิเสธการชำระเงิน');
            $this->dispatch('notify', type: 'error', message: 'คุณไม่มีสิทธิ์ในการปฏิเสธการชำระเงิน');
            return;
        }

        $payment = OrderPayment::find($paymentId);
        if ($payment && trim($reason) !== '') {
            $payment->status = 'ปฏิเสธ';
            $payment->reject_reason = $reason;
            $payment->save();
            
            // Refresh payments list
            $this->refreshPayments();
            
            session()->flash('error', 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
            $this->dispatch('notify', type: 'error', message: 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
        } else {
            session()->flash('error', 'กรุณากรอกเหตุผลในการปฏิเสธ');
        }
    }

    private function loadScannedDeliveryPayments($orderId)
    {
        $this->scannedDeliveryPayments = OrderPayment::with('order')
            ->where('order_id', $orderId)
            ->where('status', 'รอยืนยันยอด')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function refreshPayments()
    {
        // Refresh payments เฉพาะถ้ายังมี payments อยู่
        if (!empty($this->scannedDeliveryPayments)) {
            $firstPayment = collect($this->scannedDeliveryPayments)->first();
            if ($firstPayment && $firstPayment['order_id']) {
                $this->loadScannedDeliveryPayments($firstPayment['order_id']);
            }
        }
        
        // Refresh all pending payments ด้วย
        $this->loadAllPendingPayments();
    }

    private function loadAllPendingPayments()
    {
        $this->allPendingPayments = OrderPayment::with(['order.customer'])
            ->where('status', 'รอยืนยันยอด')
            ->latest('created_at')
            ->take(10) // จำกัดแค่ 10 รายการล่าสุด
            ->get();
    }

    private function loadStats()
    {
        // Load today's approved deliveries
        $this->todayApprovedDeliveries = OrderDeliverysModel::with(['order.customer'])
            ->whereDate('order_delivery_status_date', today())
            ->where('order_delivery_status', 'success')
            ->latest('order_delivery_status_date')
            ->get();

        // Load all pending payments (ทุกสลิปที่รอยืนยัน)
        $this->loadAllPendingPayments();

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
        } else {
            // ถ้าไม่ได้กรองสถานะ ให้แยกรายการที่ยืนยันแล้ววันนี้ออก
            if ($this->filterType === 'today') {
                $query->where(function($q) {
                    $q->where('order_delivery_status', '!=', 'success')
                      ->orWhere(function($subQ) {
                          $subQ->where('order_delivery_status', 'success')
                               ->whereDate('order_delivery_status_date', '!=', today());
                      });
                });
            }
        }

        // Debug: แสดง SQL query
        Log::info('Delivery Query:', [
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

