<?php

if (!function_exists('truck_type_badge')) {
    /**
     * สร้าง badge สำหรับแสดงประเภทรถขนส่ง
     */
    function truck_type_badge($truckType, $isRecommended = false): string
    {
        if (!$truckType) {
            return '<span class="badge bg-secondary">ไม่ระบุ</span>';
        }

        $colors = [
            'six_wheel_large' => 'primary',
            'six_wheel_small' => 'info', 
            'six_wheel_medium' => 'warning',
            'ten_wheel' => 'success',
        ];

        $color = $colors[$truckType->value] ?? 'secondary';
        $truckIcon = $truckType->icon();
        $starIcon = $isRecommended ? '<i class="ri-star-fill me-1"></i>' : '';
        
        return "<span class=\"badge bg-{$color}\">{$starIcon}{$truckIcon} {$truckType->description()}</span>";
    }
}

if (!function_exists('weight_display')) {
    /**
     * แสดงน้ำหนักในรูปแบบที่อ่านง่าย
     */
    function weight_display(float $weightKg): string
    {
        if ($weightKg >= 1000) {
            $ton = round($weightKg / 1000, 2);
            return number_format($ton, 2) . ' ตัน';
        }
        
        return number_format($weightKg, 2) . ' กก.';
    }
}

if (!function_exists('weight_status_badge')) {
    /**
     * แสดงสถานะน้ำหนักเทียบกับขีดจำกัดรถ
     */
    function weight_status_badge(float $weightKg, $truckType): string
    {
        if (!$truckType) {
            return '';
        }

        $capacity = $truckType->capacity();
        $percentage = ($weightKg / $capacity['max']) * 100;

        if ($percentage > 100) {
            return '<span class="badge bg-danger"><i class="ri-alert-line me-1"></i>เกินขีดจำกัด</span>';
        } elseif ($percentage > 80) {
            return '<span class="badge bg-warning"><i class="ri-alert-line me-1"></i>ใกล้เต็ม</span>';
        } elseif ($percentage > 50) {
            return '<span class="badge bg-info"><i class="ri-information-line me-1"></i>ปานกลาง</span>';
        } else {
            return '<span class="badge bg-success"><i class="ri-check-line me-1"></i>เหมาะสม</span>';
        }
    }
}

if (!function_exists('truck_type_icon')) {
    /**
     * แสดง icon ประเภทรถขนส่ง
     */
    function truck_type_icon($truckType, $withLabel = false): string
    {
        if (!$truckType) {
            return '❓';
        }

        $icon = $truckType->icon();
        
        if ($withLabel) {
            return $icon . ' ' . $truckType->label();
        }
        
        return $icon;
    }
}

if (!function_exists('truck_type_icon_html')) {
    /**
     * แสดง icon ประเภทรถขนส่งในรูปแบบ HTML (Font Awesome)
     */
    function truck_type_icon_html($truckType, $withLabel = false): string
    {
        if (!$truckType) {
            return '<i class="fas fa-question-circle text-muted"></i>';
        }

        $iconClass = $truckType->iconClass();
        $color = $truckType->color();
        
        $html = "<i class=\"{$iconClass} {$color}\"></i>";
        
        if ($withLabel) {
            $html .= ' ' . $truckType->label();
        }
        
        return $html;
    }
}
