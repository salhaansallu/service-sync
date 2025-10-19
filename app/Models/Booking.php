<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'tv_brand',
        'tv_model',
        'issue_type',
        'issue_description',
        'address',
        'pickup_option',
        'status',
        'timeline',
        'estimated_cost',
        'final_cost',
        'admin_notes',
    ];

    protected $casts = [
        'timeline' => 'array',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(ApiUser::class, 'user_id');
    }

    public function addTimelineEntry($status, $note = null)
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = [
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'note' => $note,
        ];
        $this->timeline = $timeline;
        $this->save();
    }
}
