<?php

namespace App\Models\products;

use Illuminate\Database\Eloquent\Model;
use App\Models\globalsets\GlobalSetValueModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductModel extends Model
{
   use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_code','product_name','product_weight','product_price','product_length','product_calculation','product_measure',
        'product_type','product_unit','product_note','product_status','product_wire_type','product_side_steel_type','product_size'
    ];

    protected $casts = [
        'product_status' => 'boolean',
        'product_weight' => 'decimal:2',
        'product_price'  => 'decimal:2',
    ];


      public function productType()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_type');
    }
       public function productWireType()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_wire_type');
    }
     public function productSideSteelType()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_side_steel_type');
    }
       public function productUnit()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_unit');
    }
      public function productMeasure()
    {
        return $this->belongsTo(GlobalSetValueModel::class, 'product_measure');
    }

}
