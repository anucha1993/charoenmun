<?php

namespace App\Http\Livewire\Customers;

use Carbon\Carbon;

use Livewire\Component;
use App\Models\customers\customerModel;

class CustomerIndex extends Component
{
    public $totalCustomers;
    public $newCustomers;
    public $inactiveCustomers;

    public function mount()
    {
        $this->loadCustomerStats();
    }

    public function loadCustomerStats()
    {
        // จำนวนลูกค้าทั้งหมด
        $this->totalCustomers = customerModel::count();

        // ลูกค้าใหม่ (3 เดือนล่าสุด)
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $this->newCustomers = customerModel::where('created_at', '>=', $threeMonthsAgo)->count();

        // ลูกค้าที่ไม่มีการเคลื่อนไหว (ไม่มีการสั่งซื้อใน 6 เดือน)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $this->inactiveCustomers = customerModel::whereDoesntHave('orders', function($query) use ($sixMonthsAgo) {
            $query->where('created_at', '>=', $sixMonthsAgo);
        })->count();
    }

    public function render()
    {
        return view('livewire.customers.customer-index');
    }
}
