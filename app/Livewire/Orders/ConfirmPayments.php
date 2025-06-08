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
        $this->payments = OrderPayment::with('orderDelivery')->where('status', 'รอยืนยันยอด')->latest()->get();
    }

    public function confirm($paymentId)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment) {
            $payment->status = 'ชำระเงินแล้ว';
            $payment->save();
            $this->loadPayments();

            $delivery = OrderDeliverysModel::find($payment->order_delivery_id);
            $delivery->updatePaymentStatus();
            // ✅ อัปเดตสถานะบิลหลัก (สำคัญ!)

            $order = $payment->order;
            $payment->order->updatePaymentStatus();

            session()->flash('success', 'ยืนยันสลิปเรียบร้อยแล้ว ✅');
        }
    }

    public function reject($paymentId)
    {
        $payment = OrderPayment::find($paymentId);
        if ($payment) {
            $payment->status = 'ไม่ผ่านการตรวจสอบ';
            $payment->save();
            $this->loadPayments();
            session()->flash('error', 'ปฏิเสธการชำระเงินเรียบร้อย ❌');
        }
    }

    public function render()
    {
        return view('livewire.orders.confirm-payments')->layout('layouts.horizontal', ['title' => 'ยืนยันการชำระเงิน']);
    }
}
