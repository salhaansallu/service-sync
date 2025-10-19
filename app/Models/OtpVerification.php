<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'is_verified',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isValid($otp)
    {
        return !$this->isExpired() && 
               !$this->is_verified && 
               $this->otp === $otp && 
               $this->attempts < 5;
    }
}
