<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['regular_customer_id', 'irregular_customer_id','customer_name', 'bill_type', 'bill_date', 'final_amount'];

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
}

