<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'irregular_customer_id',
        'customer_name',
        'quotation_date',
        'final_amount',
    ];

    protected $attributes = [
        'final_amount' => 0, // Default final amount, adjust as needed
    ];

    public function irregularCustomer()
    {
        return $this->belongsTo(IrregularCustomer::class, 'irregular_customer_id');
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
