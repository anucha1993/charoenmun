<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\User;
use App\Models\Quotations\QuotationModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class SalesReport extends Component
{
    public $dateFrom;
    public $dateTo;
    public $selectedSales = []; // Array ของ sale IDs ที่เลือก
    public $showAllSales = true; // แสดง sale ทุกคนหรือไม่
    
    public $salesData = [];
    public $summaryData = [];

    public function mount()
    {
        // ตั้งค่า default date range (เดือนปัจจุบัน)
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        
        $this->loadData();
    }

    public function updatedDateFrom()
    {
        $this->loadData();
    }

    public function updatedDateTo()
    {
        $this->loadData();
    }

    public function updatedSelectedSales()
    {
        $this->showAllSales = empty($this->selectedSales);
        $this->loadData();
    }

    public function toggleShowAll()
    {
        $this->showAllSales = !$this->showAllSales;
        if ($this->showAllSales) {
            $this->selectedSales = [];
        }
        $this->dispatch('showAllToggled', showAll: $this->showAllSales);
        $this->loadData();
    }

    public function loadData()
    {
        $query = QuotationModel::with('sale')
            ->whereBetween('created_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59']);

        // กรอง sale ที่เลือก
        if (!$this->showAllSales && !empty($this->selectedSales)) {
            $query->whereIn('created_by', $this->selectedSales);
        }

        $quotations = $query->get();

        // คำนวณสถิติแยกตาม sale
        $salesStats = $quotations->groupBy('created_by')->map(function ($items, $saleId) {
            $total = $items->count();
            
            // นับสถานะโดยใช้ enum value
            $wait = $items->filter(function($item) {
                return $item->quote_status->value === 'wait';
            })->count();
            
            $success = $items->filter(function($item) {
                return $item->quote_status->value === 'success';
            })->count();
            
            $cancel = $items->filter(function($item) {
                return $item->quote_status->value === 'cancel';
            })->count();
            
            $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 0;
            $cancelRate = $total > 0 ? round(($cancel / $total) * 100, 2) : 0;
            
            // คำนวณยอดขายรวม (เฉพาะที่อนุมัติ)
            $totalAmount = $items->filter(function($item) {
                return $item->quote_status->value === 'success';
            })->sum('quote_grand_total');

            return [
                'sale' => $items->first()->sale,
                'total' => $total,
                'wait' => $wait,
                'success' => $success,
                'cancel' => $cancel,
                'success_rate' => $successRate,
                'cancel_rate' => $cancelRate,
                'total_amount' => $totalAmount,
            ];
        })->sortByDesc('success')->values();

        $this->salesData = $salesStats->toArray();

        // คำนวณสถิติรวม
        $totalAll = $quotations->count();
        
        $waitAll = $quotations->filter(function($item) {
            return $item->quote_status->value === 'wait';
        })->count();
        
        $successAll = $quotations->filter(function($item) {
            return $item->quote_status->value === 'success';
        })->count();
        
        $cancelAll = $quotations->filter(function($item) {
            return $item->quote_status->value === 'cancel';
        })->count();
        
        $successRateAll = $totalAll > 0 ? round(($successAll / $totalAll) * 100, 2) : 0;
        $cancelRateAll = $totalAll > 0 ? round(($cancelAll / $totalAll) * 100, 2) : 0;
        
        $totalAmountAll = $quotations->filter(function($item) {
            return $item->quote_status->value === 'success';
        })->sum('quote_grand_total');

        $this->summaryData = [
            'total' => $totalAll,
            'wait' => $waitAll,
            'success' => $successAll,
            'cancel' => $cancelAll,
            'success_rate' => $successRateAll,
            'cancel_rate' => $cancelRateAll,
            'total_amount' => $totalAmountAll,
        ];
    }

    public function exportExcel()
    {
        $filename = 'sales-report-' . $this->dateFrom . '-to-' . $this->dateTo . '.xlsx';
        
        return Excel::download(
            new SalesReportExport($this->salesData, $this->summaryData, $this->dateFrom, $this->dateTo),
            $filename
        );
    }

    public function render()
    {
        // ดึงรายชื่อ Sale ทั้งหมดสำหรับ filter
        $allSales = User::whereHas('quotations')->orderBy('name')->get();

        return view('livewire.reports.sales-report', compact('allSales'))
            ->layout('layouts.horizontal', ['title' => 'รายงานสถิติการขาย']);
    }
}
