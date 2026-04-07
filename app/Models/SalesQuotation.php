<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesQuotation extends Model
{
    use HasFactory;

    protected $table = 'sales_quotations';

    protected $fillable = [
        'sq_no',
        'customer_name',
        'customer_phone',
        'items',
        'total',
        'note',
        'pos_code',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
    ];
}
