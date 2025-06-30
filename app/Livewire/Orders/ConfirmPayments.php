<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderPayment;
use App\Models\Orders\OrderDeliverysModel;

class ConfirmPayments extends Component
{
    public $payments;

    public function mount()
    {
        $this->loadPayments();
    }

    public function loadPayments()
    {
        $this->payments = OrderPayment::with('order')->where('status', 'รอยืนยันยอด')->latest()->get();

        $this->stats = [
            'pending_count' => OrderPayment::where('status', 'รอยืนยันยอด')->count(),
            'pending_amount' => OrderPayment::where('status', 'รอยืนยันยอด')->sum('amount'),
            'approved_count' => OrderPayment::where('status', 'ชำระเงินแล้ว')->count(),
            'approved_amount' => OrderPayment::where('status', 'ชำระเงินแล้ว')->sum('amount'),
            'rejected_count' => OrderPayment::where('status', 'ปฏิเสธ')->count(),
        ];
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

    public function reject($paymentId)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment) {
            $payment->status = 'ปฏิเสธ';
            $payment->save();
            $this->loadPayments();
            session()->flash('error', 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
        }
    }

    public function render()
    {
        return view('livewire.orders.confirm-payments', [
            'stats' => $this->stats,
        ])->layout('layouts.horizontal', ['title' => 'ยืนยันการชำระเงิน']);
    }
}
