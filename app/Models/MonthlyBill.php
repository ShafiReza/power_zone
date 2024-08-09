<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyBill extends Model
{
    use HasFactory;
    protected $fillable = [
        'regular_customer_id',
        'customer_address',
        'amount',
        'description',
        'service',
        'bill_month',
        'start_date',
        'status',
        'type',
        'due_amount',
        'next_generation_date',
    ];

    protected $casts = [
        'bill_month' => 'date:Y-m',
        'start_date' => 'date',
        'next_generation_date' => 'date',
    ];

    public function regularCustomer()
    {
        return $this->belongsTo(RegularCustomer::class, 'regular_customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id'); // Make sure 'bill_id' matches the column in the payments table
    }
}
