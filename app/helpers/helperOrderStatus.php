<?php

if (! function_exists('order_status_badge')) {
    /**
     * คืนค่า HTML badge สำหรับสถานะใบสั่งซื้อ
     *
     * @param  string  $status
     * @return string
     */
    function order_status_badge(string $status): string
    {
        return match (strtolower($status)) {
            'open'        => '<span class="badge bg-primary">เปิดรายการ</span>',
            'processing'  => '<span class="badge bg-info text-dark">กำลังดำเนินการ</span>',
            'shipped'     => '<span class="badge bg-success">จัดส่งแล้ว</span>',
            'delivered'   => '<span class="badge bg-success">ส่งถึงแล้ว</span>',
            'cancelled'   => '<span class="badge bg-danger">ยกเลิกแล้ว</span>',
            'returned'    => '<span class="badge bg-warning text-dark">ส่งคืนแล้ว</span>',
            default       => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
        };
    }
}
