<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem2 extends Model
{
    use HasFactory;
    protected $fillable = [
        'bill_id',
        'discount_type',
        'discount',
        'vat',
      
    ];
}
