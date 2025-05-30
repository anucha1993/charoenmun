<?php

namespace App\Livewire\Quotations;

use App\Models\Quotations\QuotationModel;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationIndex extends Component
{
    use WithPagination;

    public function delete($id)
    {
        QuotationModel::findOrFail($id)->delete();
        $this->dispatch('notify', type:'success', text:'ลบใบเสนอราคาแล้ว');
    }

    public function render()
    {
        $quotes = QuotationModel::with('customer')->latest()->paginate(15);
        return view('livewire.quotations.quotation-index', compact('quotes'))
               ->layout('layouts.horizontal', ['title'=>'Quotations']);
    }
    
}
