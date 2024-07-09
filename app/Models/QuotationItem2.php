<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem2 extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'discount_type',
        'discount',
        'vat',
        'final_amount',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
