<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\customers\CustomerModel;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected $listeners = ['deleteCustomer'];

    public function deleteCustomer($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            CustomerModel::findOrFail($id)->delete();
            session()->flash('success', 'ลบลูกค้าเรียบร้อยแล้ว');
        }
    }

    public function render()
    {
        $customers = CustomerModel::query()
            ->when($this->search, fn($query) =>
                $query->where('customer_name', 'like', "%{$this->search}%")
                      ->orWhere('customer_phone', 'like', "%{$this->search}%")
                      ->orWhere('customer_email', 'like', "%{$this->search}%")
            )
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.customers.customer-index', compact('customers'))
            ->layout('layouts.horizontal', ['title' => 'Customers - List']);
    }
}