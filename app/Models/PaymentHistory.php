<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'description',
        'receive_date',
        'bill_amount',
        'receivable_amount',
        'due_amount',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
