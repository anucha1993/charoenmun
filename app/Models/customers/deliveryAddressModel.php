<?php

namespace App\Models\customers;

use Illuminate\Database\Eloquent\Model;
use App\Models\addressList\amphuresModel;
use App\Models\addressList\districtsModel;
use App\Models\addressList\provincesModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class deliveryAddressModel extends Model
{
    use HasFactory;
    protected $table = 'delivery_address';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'delivery_number',
        'delivery_province',
        'delivery_district', 
        'delivery_amphur',
        'delivery_zipcode',
        'delivery_contact_name',
        'delivery_phone',
    ];

    // ★ บอกให้ Eloquent สร้าง 3 attribute ชื่อเหล่านี้เวลา toArray()/JSON
    protected $appends = [
        'delivery_province_name',
        'delivery_amphur_name',
        'delivery_district_name',
    ];
     public function customer() { return $this->belongsTo(customerModel::class); }

     /* province */
    public function getDeliveryProvinceNameAttribute()
    {
        return provincesModel::where('province_code',$this->delivery_province)
               ->value('province_name');
    }

    /* amphur */
    public function getDeliveryAmphurNameAttribute()
    {
        return amphuresModel::where('amphur_code',$this->delivery_amphur)
               ->value('amphur_name');
    }

    /* district */
    public function getDeliveryDistrictNameAttribute()
    {
        return districtsModel::where('district_code',$this->delivery_district)
               ->value('district_name');
    }
}
