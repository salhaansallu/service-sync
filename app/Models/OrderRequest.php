<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'customer_name',
        'customer_phone',
        'products',
        'status',
        'notes',
    ];

    protected $casts = [
        'products' => 'array',
    ];
}
