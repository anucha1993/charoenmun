<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $salesData;
    protected $summaryData;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($salesData, $summaryData, $dateFrom, $dateTo)
    {
        $this->salesData = $salesData;
        $this->summaryData = $summaryData;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $data = collect();

        // เพิ่มหัวข้อรายงาน
        $data->push(['รายงานสถิติการขายของ Sale']);
        $data->push(['ช่วงเวลา: ' . $this->dateFrom . ' ถึง ' . $this->dateTo]);
        $data->push([]); // บรรทัดว่าง

        // เพิ่มสรุปรวม
        $data->push(['สรุปยอดรวม']);
        $data->push(['ใบเสนอราคาทั้งหมด', $this->summaryData['total']]);
        $data->push(['รออนุมัติ', $this->summaryData['wait']]);
        $data->push(['อนุมัติแล้ว', $this->summaryData['success'], $this->summaryData['success_rate'] . '%']);
        $data->push(['ไม่อนุมัติ', $this->summaryData['cancel'], $this->summaryData['cancel_rate'] . '%']);
        $data->push(['ยอดขายรวม (บาท)', number_format($this->summaryData['total_amount'], 2)]);
        $data->push([]); // บรรทัดว่าง

        // เพิ่มข้อมูล Sale แต่ละคน
        foreach ($this->salesData as $sale) {
            $data->push([
                $sale['sale']['name'] ?? 'N/A',
                $sale['total'],
                $sale['wait'],
                $sale['success'],
                $sale['cancel'],
                $sale['success_rate'] . '%',
                $sale['cancel_rate'] . '%',
                number_format($sale['total_amount'], 2),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'ชื่อ Sale',
            'ทั้งหมด',
            'รออนุมัติ',
            'อนุมัติแล้ว',
            'ไม่อนุมัติ',
            'อัตราความสำเร็จ',
            'อัตราไม่อนุมัติ',
            'ยอดขายรวม (บาท)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // จัดรูปแบบหัวข้อหลัก
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'],
            ],
        ]);

        // จัดรูปแบบช่วงเวลา
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // จัดรูปแบบส่วนสรุป
        $sheet->mergeCells('A4:H4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBEAFE'],
            ],
        ]);

        // จัดรูปแบบหัวตาราง (แถวที่ 11)
        $headerRow = 11;
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '9CA3AF'],
                ],
            ],
        ]);

        // จัดรูปแบบข้อมูล
        $lastRow = 11 + count($this->salesData);
        $sheet->getStyle("A12:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // จัดให้ตัวเลขชิดขวา
        $sheet->getStyle("B12:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 12,
            'C' => 12,
            'D' => 15,
            'E' => 12,
            'F' => 18,
            'G' => 18,
            'H' => 20,
        ];
    }

    public function title(): string
    {
        return 'รายงานสถิติการขาย';
    }
}
