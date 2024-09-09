<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'bill_id', 'product_id', 'non_inventory_id',
        'product_name', 'description', 'quantity',
        'unit_price', 'discount', 'total_amount',
        'brand_name', 'origin' // Add brand_name and origin
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function nonInventory()
    {
        return $this->belongsTo(NonInventory::class, 'non_inventory_id');
    }
}
