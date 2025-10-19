<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ApiUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'api_users';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'phone_verified',
        'notification_preferences',
        'customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified' => 'boolean',
        'notification_preferences' => 'array',
        'password' => 'hashed',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(customers::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
