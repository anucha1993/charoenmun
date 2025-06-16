<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\customers\customerModel;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected $listeners = ['deleteCustomer'];


 public function __construct(array $data = [])
    {
        // ไม่ต้องทำอะไรกับ $data ก็ได้
    }

    public function updatingSearch() { $this->resetPage(); }

  public function deleteCustomer(int $id): void
{
    if ($id) {
        customerModel::findOrFail($id)->delete();
         $this->dispatch('notify', message: 'ลบลูกค้าเรียบร้อยแล้ว');

    }
}

    public function render()
    {
        $customers = customerModel::query()
            ->when($this->search, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('customer_name',  'like', "%{$this->search}%")
                      ->orWhere('customer_phone', 'like', "%{$this->search}%")
                      ->orWhere('customer_email', 'like', "%{$this->search}%");
                })
            )
            ->latest('id')
            ->paginate(15);

        return view('livewire.customers.customer-index', compact('customers'))
               ->layout('layouts.horizontal', ['title' => 'Customers - List']);
    }
}
