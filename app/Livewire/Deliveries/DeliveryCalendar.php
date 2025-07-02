<?php

namespace App\Livewire\Deliveries;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Orders\OrderDeliverysModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use App\Enums\TruckType;

#[Layout('layouts.horizontal')]
class DeliveryCalendar extends Component
{
    public $month;
    public $year;
    public $calendar = [];
    public $deliveriesByDate = [];

    public function mount()
    {
        // Default to current month and year
        $this->month = request('month', now()->month);
        $this->year = request('year', now()->year);
        
        $this->loadCalendarData();
    }

    public function loadCalendarData()
    {
        // Generate calendar dates for the selected month
        $startOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Add days to include the full weeks (start from previous month's days if needed)
        $startDay = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        // Create a period for all days in the calendar view
        $period = CarbonPeriod::create($startDay, $endDay);
        
        // Build the calendar array
        $calendar = collect($period)->map(function ($date) {
            return [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'isCurrentMonth' => $date->month == $this->month,
                'isToday' => $date->isToday(),
            ];
        })->chunk(7)->toArray();
        
        $this->calendar = $calendar;
        
        // Load deliveries for this month
        $deliveries = OrderDeliverysModel::with(['order', 'order.customer'])
            ->whereBetween('order_delivery_date', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
            ->get();
        
        // Process delivery data to include required fields
        $processedDeliveries = $deliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'document_no' => $delivery->order_delivery_number,
                'delivery_date' => $delivery->order_delivery_date,
                'truck_type' => $delivery->selected_truck_type instanceof TruckType ? $delivery->selected_truck_type->label() : 'ไม่ระบุ',
                'total_weight' => number_format($delivery->total_weight_kg, 2),
                'order_id' => $delivery->order_id,
                'order_number' => $delivery->order->order_number ?? '-',
                'customer_name' => $delivery->order->customer->customer_name ?? '-',
                'delivery_status' => $delivery->order_delivery_status,
            ];
        });
        
        // Group deliveries by date
        $this->deliveriesByDate = $processedDeliveries->groupBy(function($delivery) {
            return Carbon::parse($delivery['delivery_date'])->format('Y-m-d');
        })->toArray();
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->loadCalendarData();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->loadCalendarData();
    }

    public function render()
    {
        $currentMonthName = Carbon::createFromDate($this->year, $this->month, 1)->locale('th')->monthName;
        $buddhistYear = $this->year + 543;
        
        return view('livewire.deliveries.delivery-calendar', [
            'title' => 'ปฏิทินจัดส่งสินค้า',
            'currentMonthName' => $currentMonthName,
            'buddhistYear' => $buddhistYear
        ]);
    }
}
