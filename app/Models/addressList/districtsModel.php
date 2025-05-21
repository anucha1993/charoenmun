<?php

namespace App\Models\addressList;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class districtsModel extends Model
{
     use HasFactory;
     //
     protected $table = 'districts';
     protected $primaryKey = 'id';

     protected $fillable = [
        'district_name',
        'district_code',
        'amphur_code',
        'province_code',
        'zipcode',
    ];
}
