<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDiscount extends Model
{
    use HasFactory;
    protected $fillable = ['bill_id', 'discount_type', 'discount_amount'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

}
