<?php

namespace App\Livewire\Customers;

use Livewire\Component;

class CustomerCreate extends Component
{
    public function render()
    {
        return view('livewire.customers.customer-create')->layout('layouts.horizontal', ['title' => 'Customers-Create']);
    }
}
