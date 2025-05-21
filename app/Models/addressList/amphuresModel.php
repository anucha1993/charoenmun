<?php

namespace App\Models\addressList;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class amphuresModel extends Model
{
     use HasFactory;
     protected $table = 'amphures';
     protected $primaryKey = 'id';

     protected $fillable = [
        'amphur_name',
        'amphur_code',
        'province_code',
    ];
}
