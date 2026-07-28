<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_coupon_id', 'pos_code', 'bill_no', 'redeemed_by',
        'redeemed_at', 'paid_by', 'paid_at',
    ];

    protected $casts = ['redeemed_at' => 'datetime', 'paid_at' => 'datetime'];

    public function coupon()
    {
        return $this->belongsTo(ReferralCoupon::class, 'referral_coupon_id');
    }
}
