<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'regular_customer_id',
        'irregular_customer_id',
        'customer_name',
        'bill_type',
        'bill_date',
        'final_amount',
        'amount',
        'status',
        'billing_month',
        'amount',
        'bill_month',
        'start_date',
        'status'
    ];
    protected $attributes = [
        'billing_month' => null, // Default value, adjust as needed
    ];
    protected $casts = [
        'billing_month' => 'date:Y-m', // Cast billing_month to date format Y-m
    ];

    public function regularCustomer()
    {
        return $this->belongsTo(RegularCustomer::class, 'regular_customer_id');
    }

    public function irregularCustomer()
    {
        return $this->belongsTo(IrregularCustomer::class, 'irregular_customer_id');
    }

    // Accessor method to get customer name based on type
    public function getCustomerNameAttribute()
    {
        if ($this->regular_customer_id) {
            return $this->regularCustomer->name;
        } elseif ($this->irregular_customer_id) {
            return $this->irregularCustomer->name;
        }

        return null;
    }

    public function customer()
    {
        return $this->belongsTo(RegularCustomer::class);
    }
    public function monthly_bill()
    {
        return $this->hasMany(Bill::class);
    }
    public function scopeMonthlyBill($query)
    {
        return $query->select('id', 'regular_customer_id', 'amount', 'bill_month', 'start_date', 'status');
    }

    // Scope for general bills
    public function scopeGeneralBill($query)
    {
        return $query->select('id', 'regular_customer_id', 'irregular_customer_id', 'customer_name', 'bill_type', 'bill_date', 'final_amount', 'amount', 'status', 'billing_month');
    }
}

