<?php
namespace App\Livewire\Quotations;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Quotations\QuotationModel;
use App\Models\Orders\OrderModel;
use App\Models\Orders\OrderItemsModel;

class QuotationsShow extends Component
{
   public ?QuotationModel $quotation = null;

    protected $listeners = [
        // เมื่อ dispatch('approveQuotation', { quotationId: … }) เข้ามา
        'approveQuotation' => 'approveQuotation'
    ];

    public function mount(?int $id = null)
    {
        if ($id) {
            // โหลดใบเสนอราคาพร้อม relationship ตามต้องการ
            $this->quotation = QuotationModel::with(['items','customer','deliveryAddress','sale'])
                                             ->find($id);
        }
    }

   public function render()
    {
        return view('livewire.orders.order-show', [
            'order' => $this->order
        ])->layout('layouts.horizontal', ['title' => 'รายละเอียด Order']);
    }

    /**
     * Livewire จะส่งค่า $quotationId ตรงๆ
     * (จาก wire:click="$dispatch('approveQuotation', { quotationId: … })")
     */
    public function approveQuotation($quotationId)
    {
        if (! $quotationId) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'ไม่พบข้อมูลใบเสนอราคา'
            ]);
            return;
        }

        $this->quotation = QuotationModel::with([
            'items', 
            'customer', 
            'deliveryAddress', 
            'sale'
        ])->find($quotationId);

        if (! $this->quotation) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'ไม่พบใบเสนอราคาที่ร้องขอ'
            ]);
            return;
        }

        if ($this->quotation->status !== 'wait') {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'ใบเสนอราคานี้ไม่สามารถอนุมัติได้อีก'
            ]);
            return;
        }

        DB::transaction(function () {
            $orderNumber = $this->generateOrderNumber();

            $order = OrderModel::create([
                'quote_id'            => $this->quotation->id,
                'order_number'        => $orderNumber,
                'order_date'          => now()->toDateString(),
                'customer_id'         => $this->quotation->customer_id,
                'delivery_address_id' => $this->quotation->delivery_address_id,
                'subtotal'            => $this->quotation->quote_subtotal,
                'discount'            => $this->quotation->quote_discount,
                'vat'                 => $this->quotation->quote_vat,
                'grand_total'         => $this->quotation->quote_grand_total,
                'payment_status'      => 'pending',
                'status'              => 'open',
                'created_by'          => Auth::id(),
                'updated_by'          => Auth::id(),
            ]);

            foreach ($this->quotation->items as $qItem) {
                OrderItemsModel::create([
                    'order_id'            => $order->id,
                    'product_id'          => $qItem->product_id,
                    'product_name'        => $qItem->product_name,
                    'product_type'        => $qItem->product_type,
                    'product_unit'        => $qItem->product_unit,
                    'product_detail'      => $qItem->product_detail,
                    'product_length'      => $qItem->product_length,
                    'product_weight'      => $qItem->product_weight,
                    'product_calculation' => $qItem->product_calculation,
                    'quantity'            => $qItem->quantity,
                    'unit_price'          => $qItem->unit_price,
                    'total'               => $qItem->total,
                ]);
            }

            $this->quotation->update(['status' => 'approved']);

            $this->dispatch('notify', [
                'type'    => 'success',
                'message' => 'สร้างออร์เดอร์เรียบร้อย เลขที่: ' . $orderNumber
            ]);

            redirect()->route('orders.show', $order->id);
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'OR' . now()->format('ym');
        $last   = OrderModel::where('order_number', 'like', $prefix . '%')
                            ->orderBy('order_number', 'desc')
                            ->value('order_number');

        $next = $last
            ? ((int) substr($last, -4)) + 1
            : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
