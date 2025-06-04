<?php
// app/Helpers/helperStatus.php

if (! function_exists('payment_status_badge')) {
    /**
     * คืนค่า HTML badge สำหรับสถานะการชำระเงิน
     *
     * @param  string  $status
     * @return string
     */
    function payment_status_badge(string $status): string
    {
        return match (strtolower($status)) {
            'pending'  => '<span class="badge bg-warning">รอชำระ</span>',
            'paid'     => '<span class="badge bg-success">ชำระแล้ว</span>',
            'failed'   => '<span class="badge bg-danger">ไม่สำเร็จ</span>',
            'refunded' => '<span class="badge bg-secondary">คืนเงินแล้ว</span>',
            default    => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
        };
    }
}

