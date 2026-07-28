<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_code', 'code', 'referrer_phone', 'amount', 'status', 'created_by',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function redemption()
    {
        return $this->hasOne(ReferralCouponRedemption::class);
    }
}
