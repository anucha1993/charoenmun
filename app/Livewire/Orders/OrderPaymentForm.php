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
    public string $payment_type = 'transfer'; // 'transfer', 'cash', 'pocket_money'
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
    public $customerPocketMoney = 0;

    public function mount(OrderModel $order)
    {
        $this->order = $order->load('customer');
        $this->grandTotal = $order->order_grand_total;
        $this->paidAmount = $order->payments()->where('status', 'ชำระเงินแล้ว')->sum('amount');
        $this->outstanding = max(0, $this->grandTotal - $this->paidAmount);
        $this->customerPocketMoney = $order->customer ? $order->customer->customer_pocket_money : 0;
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

    public function updatedPaymentType($value)
    {
        if ($value === 'pocket_money') {
            $this->mode = 'manual';
            // เคลียร์ข้อมูลเก่า
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
    }

    public function updatedMode($value)
    {
        if ($value !== 'manual' && $this->payment_type === 'pocket_money') {
            $this->payment_type = 'transfer';
        }
    }

    public function submit()
    {
        // Validation ตามประเภทการชำระ
        if ($this->payment_type === 'pocket_money') {
            $this->validate([
                'manual.amount' => 'required|numeric|min:1|max:' . $this->customerPocketMoney,
            ], [
                'manual.amount.max' => 'จำนวนเงินเกินวงเงิน Pocket Money ที่มี (' . number_format($this->customerPocketMoney, 2) . ' บาท)',
            ]);
            
            if ($this->customerPocketMoney <= 0) {
                session()->flash('error', 'ลูกค้าไม่มียอด Pocket Money');
                return;
            }
        } elseif ($this->mode === 'manual' && in_array($this->payment_type, ['transfer', 'cash'])) {
            $this->validate([
                'slip' => 'required|image|max:2048',
                'manual.amount' => 'required|numeric|min:1',
                'manual.sender_name' => $this->payment_type === 'transfer' ? 'required|string' : '',
                'manual.transfer_at' => $this->payment_type === 'transfer' ? 'required|date' : '',
            ]);
        } else {
            $this->validate([
                'slip' => 'required|image|max:2048',
            ]);
            if (empty($this->slipData)) {
                session()->flash('error', 'กรุณาแนบสลิปให้ถูกต้องก่อนบันทึก');
                return;
            }
        }

        // สร้าง payment record
        $paymentData = [
            'order_id' => $this->order->id,
            'user_id' => Auth::id(),
            'payment_type' => $this->payment_type,
            'amount' => 0,
            'status' => 'รอยืนยันยอด',
        ];

        if ($this->payment_type === 'pocket_money') {
            // การชำระด้วย Pocket Money
            $paymentData['amount'] = $this->manual['amount'];
            $paymentData['sender_name'] = $this->order->customer->customer_contract_name ?? $this->order->customer->customer_name;
            $paymentData['receiver_name'] = 'Pocket Money';
            $paymentData['transfer_at'] = now();
            $paymentData['slip_path'] = null; // ไม่ต้องมีสลิป
            $paymentData['status'] = 'ชำระเงินแล้ว'; // อนุมัติทันที
            
            // หักยอด Pocket Money
            $this->order->customer->decrement('customer_pocket_money', $this->manual['amount']);
            
        } else {
            // การชำระแบบปกติ (transfer/cash)
            $storedPath = $this->slip->store('payments', 'public');
            $paymentData['slip_path'] = $storedPath;
            
            if ($this->mode === 'manual') {
                $paymentData['amount'] = $this->manual['amount'] ?? 0;
                $paymentData['sender_name'] = $this->manual['sender_name'] ?? '';
                $paymentData['bank_name'] = $this->manual['bank_name'] ?? '';
                $paymentData['sender_account_no'] = $this->manual['account_no'] ?? '';
                $paymentData['receiver_name'] = $this->manual['receiver_name'] ?? '';
                $paymentData['receiver_account_no'] = $this->manual['receiver_account_no'] ?? '';
                $paymentData['transfer_at'] = $this->manual['transfer_at'] ?? now();
            } else {
                $paymentData['amount'] = $this->slipData['amount'];
                $paymentData['sender_name'] = $this->slipData['sender_name'] ?? '';
                $paymentData['receiver_name'] = $this->slipData['receiver_name'] ?? '';
                $paymentData['reference_id'] = $this->slipData['reference_id'] ?? null;
                $paymentData['trans_ref'] = $this->slipData['trans_ref'] ?? null;
                $paymentData['bank_name'] = $this->slipData['bank_name'] ?? null;
                $paymentData['transfer_at'] = $this->slipData['transfer_at'] ?? now();
            }
        }

        OrderPayment::create($paymentData);
        
        $message = $this->payment_type === 'pocket_money' 
            ? 'ชำระเงินด้วย Pocket Money เรียบร้อยแล้ว' 
            : 'แจ้งชำระเงินเรียบร้อยแล้ว';
            
        session()->flash('success', $message);
        return redirect()->route('orders.show', $this->order->id);
    }

    public function render()
    {
        return view('livewire.orders.order-payment-form')->layout('layouts.horizontal', ['title' => 'แจ้งชำระเงิน']);
    }
}
