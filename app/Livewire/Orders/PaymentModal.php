<?php

namespace App\Livewire\Orders;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Orders\OrderDeliverysModel;

class PaymentModal extends Component
{
    use WithFileUploads;
    public $slip;
    public $slipData = [];
    public $preview = null; // ← เพิ่มตัวนี้
    public $orderId;
    public $deliveryId;
    public $loading = false;
    public $orderNumber;
    public $orderDeliveryNumber;

    public $deliveryTotal = 0;
    public $deliveryPaid = 0;
    public $deliveryRemain = 0;

    #[On('open-payment-modal')]
    public function openPaymentModal($orderId, $deliveryId)
    {
        $this->orderId = $orderId;
        $this->deliveryId = $deliveryId;

        $this->reset(['slip', 'slipData', 'preview']);
        $order = OrderModel::find($orderId);
        $delivery = OrderDeliverysModel::find($deliveryId);
        $this->orderNumber = $order?->order_number ?? '-';
        $this->orderDeliveryNumber = $delivery?->order_delivery_number ?? '-';

        if ($delivery) {
            $this->deliveryTotal = (float) $delivery->order_delivery_grand_total;

            $this->deliveryPaid = OrderPayment::where('order_delivery_id', $deliveryId)->sum('amount');
            $this->deliveryRemain = $this->deliveryTotal - $this->deliveryPaid;
        } else {
            $this->deliveryTotal = 0;
            $this->deliveryPaid = 0;
            $this->deliveryRemain = 0;
        }
    }

    public function updatedSlip()
    {
        $this->validate([
            'slip' => 'required|image|max:2048',
        ]);

        $this->loading = true;

        $this->preview = $this->slip->temporaryUrl(); // ✅ Preview ได้ทันที

        // ตรวจสอบ Slip ผ่าน API
        $tempPath = $this->slip->store('slips_temp', 'local'); // ใช้ local เก็บไว้ชั่วคราว
        $fullPath = storage_path('app/' . $tempPath);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SLIP2GO_SECRET_KEY'),
        ])
            ->attach('file', file_get_contents($fullPath), basename($fullPath))
            ->post('https://connect.slip2go.com/api/verify-slip/qr-image/info');

        if ($response->successful() && $response->json('code') === '200000') {
            $data = $response->json('data');
            $this->slipData = [
                'amount' => $data['amount'] ?? 0,
                'sender_name' => $data['sender']['account']['name'] ?? '',
                'receiver_name' => $data['receiver']['account']['name'] ?? '',
                'transfer_at' => Carbon::parse($data['dateTime'] ?? now())->format('Y-m-d H:i:s'), // ✅ ดึงตรงจาก API
                'reference_id' => $data['referenceId'] ?? null,
                'trans_ref' => $data['transRef'] ?? null,
                'bank_name' => $data['receiver']['bank']['name'] ?? null,
            ];
            session()->flash('success', 'ตรวจสอบสลิปถูกต้อง ✅');
        } else {
            session()->flash('error', 'ตรวจสอบสลิปล้มเหลว: ' . ($response->json('message') ?? ''));
            $this->slipData = [];
        }

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.orders.payment-modal')->layout('layouts.horizontal', ['title' => 'Order Payment']);
    }

    public function submitPayment()
    {
        $this->validate([
            'slip' => 'required|image|max:2048',
        ]);

        if (empty($this->slipData)) {
            session()->flash('error', 'กรุณาแนบสลิปให้ถูกต้องก่อนบันทึก');
            return;
        }

        $this->loading = true;

        // ✅ 1. บันทึกไฟล์ลง storage แบบถาวร
        $storedPath = $this->slip->store('payments', 'public');

        // ✅ 2. บันทึกลง order_payments
        OrderPayment::create([
            'order_id' => $this->orderId,
            'order_delivery_id' => $this->deliveryId,
            'user_id' => Auth::id(),
            'slip_path' => $storedPath,
            'amount' => $this->slipData['amount'],
            'reference_id' => $this->slipData['reference_id'],
            'trans_ref' => $this->slipData['trans_ref'],
            'sender_name' => $this->slipData['sender_name'],
            'receiver_name' => $this->slipData['receiver_name'],
            'bank_name' => $this->slipData['bank_name'],
            'transfer_at' => Carbon::parse($this->slipData['transfer_at'] ?? now())->format('Y-m-d H:i:s'),
            'status' => 'รอยืนยันยอด',
        ]);

        // ✅ 3. อัปเดตสถานะการชำระเงินของบิลย่อย
        $delivery = OrderDeliverysModel::find($this->deliveryId);
        $delivery->payment_status = 'waiting_confirmation';
        $delivery->save();

        // แจ้งให้ order-show รู้ว่ามีการสร้าง payment แล้ว
        $this->dispatch('payment-created');

        // ปิด modal
        $this->dispatch('close-payment-modal');

        $this->reset(['slip', 'slipData', 'preview']);
        $this->dispatch('notify', type: 'success', message: 'แจ้งชำระเงินเรียบร้อยแล้ว');
        // session()->flash('success', 'บันทึกข้อมูลการชำระเงินเรียบร้อยแล้ว ✅');

        $this->loading = false;
    }
}
