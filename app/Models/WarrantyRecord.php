<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WarrantyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'bill_number',
        'phone_number',
        'product_name',
        'purchase_date',
        'expiry_date',
        'coverage_type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        return $this->is_active && $this->expiry_date->isFuture();
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->isValid()) {
            return 0;
        }
        return now()->diffInDays($this->expiry_date, false);
    }
}
