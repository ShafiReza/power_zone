<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'description',
        'receiveable_amount',
        'due_amount',
        'updated_at',
        'created_at'
    ];

    public function bill()
{
    return $this->belongsTo(MonthlyBill::class, 'bill_id'); // Make sure 'bill_id' matches the column in the payments table
}
}
