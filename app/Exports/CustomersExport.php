<?php

namespace App\Exports;

use App\Models\customers\customerModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;
    protected $exportType;

    public function __construct($search = '', $exportType = 'all')
    {
        $this->search = $search;
        $this->exportType = $exportType;
    }

    public function query()
    {
        $query = customerModel::query();

        // Apply search filter if exists
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_phone', 'like', "%{$this->search}%")
                  ->orWhere('customer_email', 'like', "%{$this->search}%");
            });
        }

        // Apply type filter
        switch ($this->exportType) {
            case 'new':
                $threeMonthsAgo = Carbon::now()->subMonths(3);
                $query->where('created_at', '>=', $threeMonthsAgo);
                break;
            
            case 'inactive':
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $query->whereNotExists(function ($q) use ($sixMonthsAgo) {
                    $q->select(DB::raw(1))
                      ->from('orders')
                      ->whereRaw('orders.customer_id = customers.id')
                      ->where('orders.created_at', '>=', $sixMonthsAgo);
                });
                break;
            
            default: // 'all'
                // No additional filter needed
                break;
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'รหัสลูกค้า',
            'ชื่อลูกค้า',
            'ชื่อผู้ติดต่อ',
            'เบอร์โทร',
            'อีเมล',
            'ที่อยู่',
            'ประเภทลูกค้า',
            'ระดับลูกค้า',
            'วันที่สร้าง'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->customer_name,
            $customer->customer_contract_name,
            $customer->customer_phone,
            $customer->customer_email,
            $customer->customer_address,
            $customer->type->value,
            $customer->level->value,
            $customer->created_at->format('d/m/Y')
        ];
    }
}
