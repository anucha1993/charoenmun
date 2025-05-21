<?php

namespace App\Livewire\Customers;

use App\Models\customers\CustomerModel;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerIndex extends Component
{
   
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap'; // หากใช้ Bootstrap

    public function render()
    {
        $customers = CustomerModel::where('customer_name', 'like', '%' . $this->search . '%')
            ->orWhere('customer_code', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.customers.customer-index', [
            'customers' => $customers,
        ])->layout('layouts.horizontal', ['title' => 'Customers-Create']);
    
    }

    public function delete($id)
    {
        CustomerModel::findOrFail($id)->delete();
        session()->flash('message', 'ลบลูกค้าเรียบร้อยแล้ว');
    }
}
