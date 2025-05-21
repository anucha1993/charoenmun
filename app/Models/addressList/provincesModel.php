<?php

namespace App\Models\addressList;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class provincesModel extends Model
{
     use HasFactory;
     //
     protected $table = 'provinces';
     protected $primaryKey = 'id';

     protected $fillable = [
        'province_name',
        'province_code',
    ];
}
