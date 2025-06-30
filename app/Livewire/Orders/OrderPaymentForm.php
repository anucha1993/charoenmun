<?php

namespace App\Livewire\Orders;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderPaymentForm extends Component
{
    use WithFileUploads;

    public OrderModel $order;
    public $slip;
    public $slipData = [];
    public $preview = null;
    public $loading = false;
    public string $mode = 'api'; // 'api' หรือ 'manual'
    public array $manual = [
        'bank_name' => '',
        'account_no' => '',
        'sender_name' => '',
        'receiver_name' => '',
        'receiver_account_no' => '',
        'transfer_at' => '',
        'amount' => 0,
    ];
    public $paidAmount = 0;
    public $outstanding = 0;
    public $grandTotal = 0;

    public function mount(OrderModel $order)
    {
        $this->order = $order;
        $this->grandTotal = $order->order_grand_total;
        $this->paidAmount = $order->payments()->where('status', 'ชำระเงินแล้ว')->sum('amount');
        $this->outstanding = max(0, $this->grandTotal - $this->paidAmount);
    }

    public function getPaymentStats()
    {
        return [
            'grand_total' => $this->grandTotal,
            'paid' => $this->paidAmount,
            'outstanding' => $this->outstanding,
        ];
    }

    public function updatedSlip()
    {
        $this->validate([
            'slip' => 'required|image|max:2048',
        ]);
        $this->preview = $this->slip?->temporaryUrl();
        if ($this->mode === 'manual') {
            // manual: แค่ preview
            return;
        }
        $this->loading = true;
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
            $this->manual['amount'] = $this->slipData['amount'];
            $this->manual['sender_name'] = $this->slipData['sender_name'];
            $this->manual['receiver_name'] = $this->slipData['receiver_name'];
            $this->manual['transfer_at'] = $this->slipData['transfer_at'];
            $this->manual['bank_name'] = $this->slipData['bank_name'];
            session()->flash('success', 'ตรวจสอบสลิปถูกต้อง ✅');
        } else {
            session()->flash('error', 'ตรวจสอบสลิปล้มเหลว: ' . ($response->json('message') ?? ''));
            $this->slipData = [];
            $this->manual = [
                'bank_name' => '',
                'account_no' => '',
                'sender_name' => '',
                'receiver_name' => '',
                'receiver_account_no' => '',
                'transfer_at' => '',
                'amount' => 0,
            ];
        }
        $this->loading = false;
    }

    public function submit()
    {
        $this->validate([
            'slip' => 'required|image|max:2048',
        ]);
        if ($this->mode === 'manual') {
            $this->validate([
                'manual.amount' => 'required|numeric|min:1',
                'manual.sender_name' => 'required|string',
                'manual.transfer_at' => 'required|date',
            ]);
        } else {
            if (empty($this->slipData)) {
                session()->flash('error', 'กรุณาแนบสลิปให้ถูกต้องก่อนบันทึก');
                return;
            }
        }
        $storedPath = $this->slip->store('payments', 'public');
        if ($this->mode === 'manual') {
            OrderPayment::create([
                'order_id' => $this->order->id,
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
                'order_id' => $this->order->id,
                'user_id' => Auth::id(),
                'slip_path' => $storedPath,
                'amount' => $this->slipData['amount'],
                'sender_name' => $this->slipData['sender_name'] ?? '',
                'receiver_name' => $this->slipData['receiver_name'] ?? '',
                'reference_id' => $this->slipData['reference_id'] ?? null,
                'trans_ref' => $this->slipData['trans_ref'] ?? null,
                'bank_name' => $this->slipData['bank_name'] ?? null,
                'transfer_at' => $this->slipData['transfer_at'] ?? now(),
                'status' => 'รอยืนยันยอด',
            ]);
        }
        session()->flash('success', 'แจ้งชำระเงินเรียบร้อยแล้ว');
        return redirect()->route('orders.show', $this->order->id);
    }

    public function render()
    {
        return view('livewire.orders.order-payment-form')->layout('layouts.horizontal', ['title' => 'แจ้งชำระเงิน']);
    }
}
