<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name', 'bill_type', 'bill_date', 'amount', 'status'];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
}

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = ['bill_id', 'product_name', 'description', 'quantity', 'unit_price', 'discount', 'total_amount'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
