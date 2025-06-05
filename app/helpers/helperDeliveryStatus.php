<?php

if (! function_exists('order_delivery_status_badge')) {
    /**
     * คืนค่า HTML badge สำหรับสถานะใบสั่งซื้อ
     *
     * @param  string  $status
     * @return string
     */
    function order_delivery_status_badge(string $status): string
    {
        return match (strtolower($status)) {
            'pending'        => '<span class="badge bg-primary">กำลังจัดส่ง</span>',
            'procesหsing'  => '<span class="badge bg-info text-dark">กำลังดำเนินการ</span>',
            'success'     => '<span class="badge bg-success">จัดส่งสำเร็จ</span>',
            'cancelled'   => '<span class="badge bg-danger">ยกเลิกแล้ว</span>',
            'returned'    => '<span class="badge bg-warning text-dark">ส่งคืนสินค้า</span>',
            default       => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
        };
    }
}
