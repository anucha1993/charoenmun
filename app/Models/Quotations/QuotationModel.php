<?php

namespace App\Models\Quotations;

use App\Enums\QuotationStatus;
use App\Models\customers\CustomerModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\customers\deliveryAddressModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationModel extends Model
{
    use HasFactory;
    protected $table = 'quotations';
    protected $primaryKey = 'id';
    protected $fillable = ['quotation_number', 'customer_id', 'delivery_address_id', 'quote_date', 'note', 'subtotal', 'vat', 'grand_total', 'enable_vat', 'vat_included', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'enable_vat' => 'boolean',
        'vat_included' => 'boolean',
        'quote_date' => 'date',
        'status' => QuotationStatus::class,
    ];

    // ความสัมพันธ์
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }
    public function deliveryAddress()
    {
        return $this->belongsTo(deliveryAddressModel::class, 'delivery_address_id');
    }
    public function items()
    {
        return $this->hasMany(QuotationItemModel::class, 'quotation_id');
    }
    public function getStatusBadgeAttribute(): string
    {
        /** @var QuotationStatus $status */
        $status = $this->status ?? QuotationStatus::Wait;

        return sprintf('<span class="badge %s">%s</span>', $status->badgeClass(), $status->label());
    }
}
