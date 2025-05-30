<?php
namespace App\Enums;

enum QuotationStatus: string
{
    case Wait    = 'wait';     // รอดำเนินการ
    case Success = 'success';  // ยืนยันแล้ว
    case Cancel  = 'cancel';   // ยกเลิก

    /** label ภาษาไทย */
    public function label(): string
    {
        return match($this) {
            self::Wait    => 'รอดำเนินการ',
            self::Success => 'ยืนยันแล้ว',
            self::Cancel  => 'ยกเลิกใบเสนอราคา',
        };
    }

    /** bootstrap badge class */
    public function badgeClass(): string
    {
        return match($this) {
            self::Wait    => 'bg-warning',
            self::Success => 'bg-success',
            self::Cancel  => 'bg-danger',
        };
    }
}
