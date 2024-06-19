<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularCustomer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
        'area',
        'city',
        'note',
        'initial_bill_amount',
        'start_date',
        'next_bill_date',
        'status',
    ];

    // Define the statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Define the status options
    public static $statusOptions = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    // Mutator for next bill date
    public function setNextBillDateAttribute($value)
    {
        $this->attributes['next_bill_date'] = $value; // You may adjust date format or calculate as needed
    }

    // Accessor for status
    public function getStatusAttribute($value)
    {
        return ucfirst($value); // Adjust status display format if needed
    }
}
