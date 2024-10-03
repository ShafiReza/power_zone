<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_no',
        'details',
        'brand_name',
        'category_id',
        'origin',
        'purchase_price',
        'sell_price',
        'wholesale_price',
        'quantity',
        'total_amount',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'product_name', 'name');
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}

