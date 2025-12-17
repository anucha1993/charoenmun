<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->type === 'SA';
    }

    public function isMember()
    {
        return $this->type === 'member';
    }

    // ตรวจสอบสิทธิ์ในการอนุมัติการชำระเงิน
    public function canApprovePayment()
    {
        return $this->type === 'SA';
    }

    // ตรวจสอบสิทธิ์ในการยืนยันการจัดส่ง  
    public function canConfirmDelivery()
    {
        return $this->type === 'SA';
    }

    // ตรวจสอบว่าเป็น Admin หรือ SA
    public function hasAdminAccess()
    {
        return in_array($this->type, ['admin', 'SA']);
    }

    // Relationship: User has many Quotations (as creator/sale)
    public function quotations()
    {
        return $this->hasMany(\App\Models\Quotations\QuotationModel::class, 'created_by');
    }
}
