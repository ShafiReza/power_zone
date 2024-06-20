<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillVat extends Model
{
    use HasFactory;
    protected $fillable = ['bill_id', 'vat'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
