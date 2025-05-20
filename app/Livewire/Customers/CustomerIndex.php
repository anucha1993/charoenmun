<?php

namespace App\Livewire\Customers;

use Livewire\Component;

class CustomerIndex extends Component
{
    public function render()
    {
        return view('livewire.customers.customer')->layout('layouts.horizontal', ['title' => 'Customers']);
    }
}
