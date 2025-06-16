<?php

namespace App\Models\Quotations;

use App\Models\User;
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
    protected $fillable = ['quote_number', 'customer_id', 'delivery_address_id', 'quote_date', 'quote_note', 'quote_discount', 'quote_subtotal', 'quote_vat', 'quote_grand_total', 'quote_enable_vat', 'quote_vat_included', 'quote_status', 'created_by', 'updated_by'];

    protected $casts = [
        'quote_enable_vat' => 'boolean',
        'quote_vat_included' => 'boolean',
        'quote_date' => 'date',
        'quote_status' => QuotationStatus::class,
    ];

    // ความสัมพันธ์
    public function customer()
    {
        return $this->belongsTo(customerModel::class, 'customer_id');
    }
     public function sale()
    {
        return $this->belongsTo(User::class, 'created_by');
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
        $quote_status = $this->quote_status ?? QuotationStatus::Wait;

        return sprintf('<span class="badge %s">%s</span>', $quote_status->badgeClass(), $quote_status->label());
    }
}
