<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonInventory extends Model
{
    use HasFactory;
    protected $fillable = [
       'id', 'name', 'details', 'brand_name', 'origin', 'purchase_price',
        'sell_price', 'wholesale_price', 'quantity', 'total_amount', 'status'
    ];

    public $timestamps = true;

    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'product_name', 'name');
    }
}
