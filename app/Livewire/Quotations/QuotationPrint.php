<?php
namespace App\Livewire\Quotations;

use Livewire\Component;
use App\Models\Quotations\QuotationModel;

class QuotationPrint extends Component
{
    public QuotationModel $quotation;

    public function mount(QuotationModel $quotation)
    {
        // eager load ความสัมพันธ์ที่ต้องใช้
        $quotation->load(['customer', 'deliveryAddress', 'items']);
        $this->quotation = $quotation;
    }

    public function render()
    {
        return view('livewire.quotations.print')
               ->layout('layouts.horizontal', ['title' => 'Print Quotation']);
    }
}
