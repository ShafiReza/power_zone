<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'customer_name', 'bill_type', 'bill_date', 'final_amount', 'amount'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }
    public function billItems2()
    {
        return $this->hasMany(BillItem2::class);
    }

    public function billDiscounts()
    {
        return $this->hasMany(BillDiscount::class);
    }

    public function billVats()
    {
        return $this->hasMany(BillVat::class);
    }
}

