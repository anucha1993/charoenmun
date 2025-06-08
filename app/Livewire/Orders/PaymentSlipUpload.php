<?php

namespace App\Livewire\Orders;

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;

class PaymentSlipUpload extends Component
{
    use WithFileUploads;

    public $slipImage;
    public array $slipData = [];

    public function rules()
    {
        return [
            'slipImage' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ];
    }

    public function uploadAndVerify()
    {
        $this->validate();

        $filePath = $this->slipImage->getRealPath();
        $fileName = $this->slipImage->getClientOriginalName();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SLIP2GO_SECRET_KEY'),
        ])->attach(
            'file', file_get_contents($filePath), $fileName
        )->post('https://connect.slip2go.com/api/verify-slip/qr-image/info', [
            // payload ไม่จำเป็นต้องใส่ ถ้าไม่ใช้
        ]);
        if ($response->successful()) {
            $result = $response->json();
        
            if ($result['code'] === '200000') {
                $this->slipData = $result['data'] ?? [];
        
                session()->flash('message', '✔️ ตรวจสอบสลิปสำเร็จ: ' . ($result['message'] ?? ''));
            } else {
                session()->flash('error', '❌ ตรวจสอบไม่ผ่าน: ' . ($result['message'] ?? ''));
            }
        } else {
            session()->flash('error', '❌ ไม่สามารถเชื่อมต่อ Slip2Go ได้');
        }
    }

    public function render()
    {
        return view('livewire.orders.payment-slip-upload')->layout('layouts.horizontal', ['title' => 'PaymentSlip']);
    }
}
