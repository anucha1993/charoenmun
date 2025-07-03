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
            'open', 'เปิดรายการ'                => '<span class="badge bg-primary">เปิดรายการ</span>',
            'processing'                        => '<span class="badge bg-info text-dark">กำลังดำเนินการ</span>',
            'shipped'                           => '<span class="badge bg-success">จัดส่งแล้ว</span>',
            'someproducts', 'ส่งสินค้าบางส่วน'   => '<span class="badge bg-warning text-dark">ส่งสินค้าบางส่วน</span>',
            'delivered', 'ส่งสินค้าครบแล้ว'      => '<span class="badge bg-success">ส่งสินค้าครบแล้ว</span>',
            'completed'                         => '<span class="badge bg-success">ส่งสินค้าครบแล้ว</span>',
            'cancelled'                         => '<span class="badge bg-danger">ยกเลิกแล้ว</span>',
            'returned'                          => '<span class="badge bg-warning text-dark">ส่งคืนแล้ว</span>',
            'pending'                           => '<span class="badge bg-secondary">รอดำเนินการ</span>',
            default                             => '<span class="badge bg-light text-dark">ไม่ทราบสถานะ</span>',
        };
    }
}
