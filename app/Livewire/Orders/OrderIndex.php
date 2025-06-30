<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderModel;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;
    protected $listeners = ['deleteOrder'];

    public function deleteOrder($id)
    {
        $order = \App\Models\Orders\OrderModel::find($id);
        if ($order) {
            $order->delete();
            session()->flash('message', 'ลบรายการเรียบร้อยแล้ว');
        }
    }

    public function render()
    {
        $orders = OrderModel::with(['customer', 'deliveryAddress', 'payments'])->latest()->paginate(10);

        // สรุปจำนวน
        $totalOrders = OrderModel::count();

        $paymentSummary = OrderModel::selectRaw('payment_status, COUNT(*) as total')
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $statusSummary = OrderModel::selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        return view('livewire.orders.order-index', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'paymentSummary' => $paymentSummary,
            'statusSummary' => $statusSummary,
        ])->layout('layouts.horizontal', ['title' => 'Orders']);
    }
}
