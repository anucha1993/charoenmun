<?php
namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderPayment;
use App\Models\Orders\OrderDeliverysModel;


class ConfirmPayments extends Component
{
    public $payments;
    public $filterType = 'today'; // today, pending, approved, rejected
    public $filterDate;
    public $stats;
    public $showRejectModal = false;
    public $rejectingId = null;
    public $rejectReason = '';
    public $searchOrder = '';
    public $searchOrderInput = '';

    public function hideRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingId = null;
        $this->rejectReason = '';
    }
 public function rejectWithReason($paymentId, $reason)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment && trim($reason) !== '') {
            $payment->status = 'ปฏิเสธ';
            $payment->reject_reason = $reason;
            $payment->save();
            $this->loadPayments();
            session()->flash('error', 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
        } else {
            session()->flash('error', 'กรุณากรอกเหตุผลในการปฏิเสธ');
        }
    }
    public function mount()
    {
        $this->filterDate = now()->format('Y-m-d');
        $this->loadPayments();
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
        $this->loadPayments();
    }

    public function loadPayments()
    {
        $query = OrderPayment::query();
        if ($this->searchOrder) {
            // INNER JOIN orders table and filter by order_number
            $query->join('orders', 'order_payments.order_id', '=', 'orders.id')
                  ->where('orders.order_number', 'like', '%' . $this->searchOrder . '%')
                  ->select('order_payments.*');
        } else {
            if ($this->filterType === 'today') {
                $query->whereDate('transfer_at', $this->filterDate);
            } elseif ($this->filterType === 'pending') {
                $query->where('status', 'รอยืนยันยอด')
                      ->whereDate('transfer_at', '!=', $this->filterDate);
            } elseif ($this->filterType === 'approved') {
                $query->where('status', 'ชำระเงินแล้ว');
            } elseif ($this->filterType === 'rejected') {
                $query->where('status', 'ปฏิเสธ');
            }
        }
        $this->payments = OrderPayment::with('order')->whereIn('id', $query->latest()->pluck('order_payments.id'))->get();

        // Debug: flash search value, result count, and order numbers
        if ($this->searchOrder) {
            // Clone query for debug
            $debugQuery = OrderPayment::with('order');
            if ($this->filterType === 'today') {
                $debugQuery->whereDate('transfer_at', $this->filterDate);
            } elseif ($this->filterType === 'pending') {
                $debugQuery->where('status', 'รอยืนยันยอด')
                          ->whereDate('transfer_at', '!=', $this->filterDate);
            } elseif ($this->filterType === 'approved') {
                $debugQuery->where('status', 'ชำระเงินแล้ว');
            } elseif ($this->filterType === 'rejected') {
                $debugQuery->where('status', 'ปฏิเสธ');
            }
            $debugQuery->whereHas('order', function($q) {
                $q->where('order_number', 'like', '%' . $this->searchOrder . '%');
            });
            $sql = $debugQuery->toSql();
            $bindings = json_encode($debugQuery->getBindings());
            $debugList = $this->payments->map(function($p) {
                return ($p->order ? $p->order->order_number : '-') . ' (id:' . $p->id . ', status:' . $p->status . ')';
            })->implode(', ');
            session()->flash('error', 'DEBUG: searchOrder=' . $this->searchOrder . ', results=' . $this->payments->count() . ', list=[' . $debugList . '], SQL=' . $sql . ', bindings=' . $bindings);
        }

        $this->stats = [
            'pending_count' => OrderPayment::where('status', 'รอยืนยันยอด')->count(),
            'pending_amount' => OrderPayment::where('status', 'รอยืนยันยอด')->sum('amount'),
            'approved_count' => OrderPayment::where('status', 'ชำระเงินแล้ว')->count(),
            'approved_amount' => OrderPayment::where('status', 'ชำระเงินแล้ว')->sum('amount'),
            'rejected_count' => OrderPayment::where('status', 'ปฏิเสธ')->count(),
        ];
    }

    public function updatedSearchOrderInput()
    {
        if ($this->searchOrderInput !== '') {
            $this->searchOrder = $this->searchOrderInput;
            $this->searchOrderInput = '';
            $this->loadPayments();
        }
    }

    public function confirm($paymentId)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment) {
            $payment->status = 'ชำระเงินแล้ว';
            $payment->save();
            $this->loadPayments();
            // ไม่ต้องอัปเดต delivery อีกต่อไป
            $order = $payment->order;
            $order->updatePaymentStatus();
            session()->flash('success', 'ยืนยันสลิปเรียบร้อยแล้ว ✅');
        }
    }

    public function showRejectModal($paymentId)
    {
        $this->rejectingId = $paymentId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $payment = OrderPayment::find($this->rejectingId);
        if ($payment && trim($this->rejectReason) !== '') {
            $payment->status = 'ปฏิเสธ';
            $payment->reject_reason = $this->rejectReason;
            $payment->save();
            $this->loadPayments();
            $this->showRejectModal = false;
            $this->rejectingId = null;
            $this->rejectReason = '';
            session()->flash('error', 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
        } else {
            session()->flash('error', 'กรุณากรอกเหตุผลในการปฏิเสธ');
        }
    }

        public function setPending($paymentId)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment && $payment->status === 'ปฏิเสธ') {
            $payment->status = 'รอยืนยันยอด';
            $payment->reject_reason = null;
            $payment->save();
            $this->loadPayments();
            session()->flash('success', 'เปลี่ยนสถานะเป็นรออนุมัติเรียบร้อยแล้ว');
        }
    }

    public function searchOrder()
    {
        $this->loadPayments();
    }

    public function render()
    {
        return view('livewire.orders.confirm-payments', [
            'stats' => $this->stats,
            'filterType' => $this->filterType,
            'filterDate' => $this->filterDate,
        ])->layout('layouts.horizontal', ['title' => 'ยืนยันการชำระเงิน']);
    }
}
