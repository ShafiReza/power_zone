<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IrregularCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'area',
        'city',
        'note',
        'status',
    ];

    public function customers()
    {
        return $this->morphMany(Customer::class, 'customerable');
    }

    // Define the statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Define the status options
    public static $statusOptions = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];
}
