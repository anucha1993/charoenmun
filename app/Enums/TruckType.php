<?php

namespace App\Enums;

enum TruckType: string
{
    case SIX_WHEEL_LARGE = 'six_wheel_large';    // หกล้อใหญ่ 1-5 ตัน
    case SIX_WHEEL_SMALL = 'six_wheel_small';    // หกล้อเล็ก 1-6.5 ตัน  
    case SIX_WHEEL_MEDIUM = 'six_wheel_medium';  // หกล้อกลาง 1-8 ตัน
    case TEN_WHEEL = 'ten_wheel';                // รถสิบล้อ 1-14 ตัน

    public function label(): string
    {
        return match($this) {
            self::SIX_WHEEL_LARGE => 'หกล้อใหญ่',
            self::SIX_WHEEL_SMALL => 'หกล้อเล็ก',
            self::SIX_WHEEL_MEDIUM => 'หกล้อกลาง',
            self::TEN_WHEEL => 'รถสิบล้อ',
        };
    }

    public function capacity(): array
    {
        return match($this) {
            self::SIX_WHEEL_LARGE => ['min' => 1000, 'max' => 5000],    // 1-5 ตัน (kg)
            self::SIX_WHEEL_SMALL => ['min' => 1000, 'max' => 6500],    // 1-6.5 ตัน (kg)
            self::SIX_WHEEL_MEDIUM => ['min' => 1000, 'max' => 8000],   // 1-8 ตัน (kg)
            self::TEN_WHEEL => ['min' => 1000, 'max' => 14000],         // 1-14 ตัน (kg)
        };
    }

    public function description(): string
    {
        $capacity = $this->capacity();
        $minTon = $capacity['min'] / 1000;
        $maxTon = $capacity['max'] / 1000;
        
        return $this->label() . " ({$minTon}-{$maxTon} ตัน)";
    }

    public function icon(): string
    {
        return match($this) {
            self::SIX_WHEEL_LARGE => '🚛',      // รถบรรทุกขนาดใหญ่
            self::SIX_WHEEL_SMALL => '🚚',      // รถบรรทุกขนาดเล็ก
            self::SIX_WHEEL_MEDIUM => '🚐',     // รถบรรทุกขนาดกลาง
            self::TEN_WHEEL => '🚛',           // รถบรรทุกขนาดใหญ่ (สิบล้อ)
        };
    }

    public function iconClass(): string
    {
        return match($this) {
            self::SIX_WHEEL_LARGE => 'fas fa-truck',
            self::SIX_WHEEL_SMALL => 'fas fa-shipping-fast',
            self::SIX_WHEEL_MEDIUM => 'fas fa-truck-moving',
            self::TEN_WHEEL => 'fas fa-truck-monster',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SIX_WHEEL_LARGE => 'text-blue-600',
            self::SIX_WHEEL_SMALL => 'text-green-600',
            self::SIX_WHEEL_MEDIUM => 'text-yellow-600',
            self::TEN_WHEEL => 'text-red-600',
        };
    }

    public static function getRecommendedTruck(float $weightKg): ?self
    {
        // เรียงจากน้อยไปมาก เพื่อหารถที่เหมาะสมที่สุด
        $trucks = [
            self::SIX_WHEEL_LARGE,
            self::SIX_WHEEL_SMALL,
            self::SIX_WHEEL_MEDIUM,
            self::TEN_WHEEL,
        ];

        foreach ($trucks as $truck) {
            $capacity = $truck->capacity();
            if ($weightKg >= $capacity['min'] && $weightKg <= $capacity['max']) {
                return $truck;
            }
        }

        // ถ้าน้ำหนักเกิน 14 ตัน ให้แนะนำรถสิบล้อ (หรือจะต้องใช้หลายรอบ)
        if ($weightKg > 14000) {
            return self::TEN_WHEEL;
        }

        // ถ้าน้ำหนักน้อยกว่า 1 ตัน ให้แนะนำหกล้อเล็ก
        return self::SIX_WHEEL_SMALL;
    }

    public static function getAllTrucks(): array
    {
        return [
            self::SIX_WHEEL_LARGE,
            self::SIX_WHEEL_SMALL,
            self::SIX_WHEEL_MEDIUM,
            self::TEN_WHEEL,
        ];
    }
}
