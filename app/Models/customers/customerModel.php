<?php

namespace App\Models\customers;

use App\Models\globalsets\GlobalSetValueModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = ['customer_code', 'customer_name', 'customer_type', 'customer_level', 'customer_taxid', 'customer_contract_name', 'customer_phone', 'customer_email', 'customer_idline', 'customer_address', 'customer_province', 'customer_amphur', 'customer_district', 'customer_zipcode','customer_pocket_money'];

    /* -------- รหัสอัตโนมัติ -------- */
    protected static function booted()
    {
        static::creating(function (self $customer) {
            $customer->customer_code = self::generateCode();
        });
    }

    public static function generateCode(): string
    {
        $year = now()->year;
        $prefix = "CUS-{$year}";
        /* นับจำนวนลูกค้าปีนี้ +1 */
        $lastSeq = self::where('customer_code', 'LIKE', "$prefix%")
            ->orderByDesc('customer_code')
            ->value('customer_code');

        $next = 1;
        if ($lastSeq) {
            $next = (int) substr($lastSeq, -5) + 1;
        }
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /* -------- ความสัมพันธ์ -------- */
    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddressModel::class, 'customer_id');
    }

    public function type()
    {
        return $this->belongsTo(GlobalSetValueModel::class,'customer_type');
    }

    public function level()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'customer_level');
    }
}
