<?php

namespace App\Models\Quotations;

use App\Models\products\ProductModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quotations\QuotationModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationItemModel extends Model
{
    use HasFactory;
     protected $table = 'quotation_items';
    protected $primaryKey = 'id';
     protected $fillable = [
        'quotation_id','product_id','product_name','product_type',
        'product_unit','product_length','product_weight',
        'quantity','unit_price','total','product_detail','product_calculation','product_vat','product_note'
    ];

    protected $casts = [
        'product_vat' => 'boolean',
    ];



   public function quotation()
    {
        return $this->belongsTo(QuotationModel::class, 'quotation_id');
    }
    public function product()   { return $this->belongsTo(ProductModel::class,'product_id'); }
}
