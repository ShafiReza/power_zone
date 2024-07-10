<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
}

