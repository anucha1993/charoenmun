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
    public string $mode = 'api'; // หรือ 'manual'
    public bool $showManualForm = false;
    public array $manual = [
        'bank_name' => '',
        'account_no' => '',
        'sender_name' => '',
        'receiver_name' => '',
        'receiver_account_no' => '',
        'transfer_at' => '',
        'amount' => 0,
    ];

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
    if ($this->mode === 'manual') {
        $this->preview = $this->slip?->temporaryUrl(); // แสดง preview เฉย ๆ
        return; // ❌ ไม่เรียก API ตรวจสอบ
    }

    $this->validate([
        'slip' => 'required|image|max:2048',
    ]);

    $this->loading = true;

    $this->preview = $this->slip->temporaryUrl();

    // ตรวจสอบ Slip ผ่าน API
    $tempPath = $this->slip->store('slips_temp', 'local');
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
            'transfer_at' => Carbon::parse($data['dateTime'] ?? now())->format('Y-m-d H:i:s'),
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

    if ($this->mode === 'api' && empty($this->slipData)) {
        session()->flash('error', 'กรุณาแนบสลิปให้ถูกต้องก่อนบันทึก');
        return;
    }

    $this->loading = true;

    $storedPath = $this->slip->store('payments', 'public');

    if ($this->mode === 'manual') {
        OrderPayment::create([
            'order_id' => $this->orderId,
            'order_delivery_id' => $this->deliveryId,
            'user_id' => Auth::id(),
            'slip_path' => $storedPath,
            'amount' => $this->manual['amount'] ?? 0,
            'sender_name' => $this->manual['sender_name'] ?? '',
            'bank_name' => $this->manual['bank_name'] ?? '',
            'sender_account_no' => $this->manual['account_no'] ?? '',
            'receiver_name' => $this->manual['receiver_name'] ?? '',
            'receiver_account_no' => $this->manual['receiver_account_no'] ?? '',
            'reference_id' => null,
            'trans_ref' => null,
            'transfer_at' => $this->manual['transfer_at'] ?? now(),
            'status' => 'รอยืนยันยอด',
        ]);
    } else {
        OrderPayment::create([
            'order_id' => $this->orderId,
            'order_delivery_id' => $this->deliveryId,
            'user_id' => Auth::id(),
            'slip_path' => $storedPath,
            'amount' => $this->slipData['amount'],
            'reference_id' => $this->slipData['reference_id'] ?? null,
            'trans_ref' => $this->slipData['trans_ref'] ?? null,
            'sender_name' => $this->slipData['sender_name'] ?? '',
            'sender_account_no' => $this->slipData['sender_account_no'] ?? null,
            'receiver_name' => $this->slipData['receiver_name'] ?? '',
            'receiver_account_no' => $this->slipData['receiver_account_no'] ?? null,
            'bank_name' => $this->slipData['bank_name'] ?? '',
            'transfer_at' => Carbon::parse($this->slipData['transfer_at'] ?? now())->format('Y-m-d H:i:s'),
            'status' => 'รอยืนยันยอด',
        ]);
    }

    $delivery = OrderDeliverysModel::find($this->deliveryId);
    if ($delivery) {
        $delivery->payment_status = 'waiting_confirmation';
        $delivery->save();
    }

    $this->dispatch('payment-created');
    $this->dispatch('close-payment-modal');

    $this->reset(['slip', 'slipData', 'preview', 'manual']);
    $this->dispatch('notify', type: 'success', message: 'แจ้งชำระเงินเรียบร้อยแล้ว');

    $this->loading = false;
}

}
