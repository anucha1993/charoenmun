<?php
// app/Helpers/helperStatus.php

if (! function_exists('payment_status_badge')) {
    /**
     * คืนค่า HTML badge สำหรับสถานะการชำระเงิน
     *
     * @param  string  $status
     * @return string
     */
    // function payment_status_badge(string $status): string
    // {
    //     return match (strtolower($status)) {
    //         'pending'              => '<span class="badge bg-warning">รอชำระ</span>',
    //         'waiting_confirmation' => '<span class="badge bg-info text-dark">รอตรวจสอบยอด</span>',
    //         'paid'                 => '<span class="badge bg-success">ชำระแล้ว</span>',
    //         'overpayment'          => '<span class="badge bg-danger">ชำระเงินเกินจำนวน</span>',
    //         'failed'               => '<span class="badge bg-danger">ไม่สำเร็จ</span>',
    //         'refunded'             => '<span class="badge bg-secondary">คืนเงินแล้ว</span>',
    //         default                => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
    //     };
    // }

    function payment_status_badge(string $status): string
{
    return match (strtolower($status)) {
        'pending'              => '<span class="badge bg-secondary">รอชำระ</span>',
        'waiting_confirmation' => '<span class="badge bg-warning">รอยืนยันยอด</span>',
        'partial'              => '<span class="badge bg-info">ชำระบางส่วน</span>',
        'paid'                 => '<span class="badge bg-success">ชำระแล้ว</span>',
        'overpayment'          => '<span class="badge bg-danger">ชำระเกิน</span>',
        'refunded'             => '<span class="badge bg-secondary">คืนเงินแล้ว</span>',
        'failed'               => '<span class="badge bg-danger">ไม่สำเร็จ</span>',
        default                => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
    };
}
}

