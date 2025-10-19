<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
        'status',
        'admin_notes',
    ];
}
