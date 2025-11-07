<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\Orders\OrderModel;
use App\Models\Orders\CustomerModel;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;
    protected $listeners = ['deleteOrder'];
    
    // Search and Filter properties
    public $search = '';
    public $status = '';
    
    // Protected query string parameters
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'page' => ['except' => 1]
    ];

    public function deleteOrder($id)
    {
        $order = \App\Models\Orders\OrderModel::find($id);
        if ($order) {
            $order->delete();
            session()->flash('message', 'ลบรายการเรียบร้อยแล้ว');
        }
    }
    
    // Reset page when search changes
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    // Manual refresh function
    public function refreshData()
    {
        $this->resetPage();
        session()->flash('message', 'รีเฟรชข้อมูลเรียบร้อยแล้ว');
    }
    
    // Clear all filters
    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
        session()->flash('message', 'ล้างการค้นหาเรียบร้อยแล้ว');
    }

    public function render()
    {
        // Build query with search and filter
        $query = OrderModel::with(['customer', 'deliveryAddress', 'payments']);
        
        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($customerQuery) {
                      $customerQuery->where('customer_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                                   ->orWhere('customer_address', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('deliveryAddress', function ($deliveryQuery) {
                      $deliveryQuery->where('delivery_contact_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('delivery_phone', 'like', '%' . $this->search . '%')
                                   ->orWhere('delivery_address', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Apply status filter
        if (!empty($this->status)) {
            $query->where('order_status', $this->status);
        }
        
        $orders = $query->latest()->paginate(10);

        // สรุปจำนวน (ใช้ query ที่มีการกรองแล้ว)
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
        ]);
    }
}
