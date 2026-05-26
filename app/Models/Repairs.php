<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repairs extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'updated_at', 'invoice', 'paid_at', 'delivery', 'warranty', 'service_warranty', 'signature'];

    public function credit()
    {
        return $this->belongsTo(Credit::class, 'bill_no', 'order_id');
    }
}
