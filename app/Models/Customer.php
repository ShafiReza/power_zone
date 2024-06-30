<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'customer_type',
        'customer_name',
        'email',
        'phone'
    ];

    public function customerable()
    {
        return $this->morphTo();
    }
}
