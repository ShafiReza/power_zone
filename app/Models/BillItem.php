<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;
    protected $fillable = ['bill_id','product_name', 'description', 'quantity', 'unit_price', 'discount', 'total_amount'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
