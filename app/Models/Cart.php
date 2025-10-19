<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(ApiUser::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function calculateSummary()
    {
        $items = $this->items()->with('product')->get();
        $itemCount = $items->sum('quantity');
        $subtotal = $items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        $tax = 0; // You can add tax calculation here
        $total = $subtotal + $tax;

        return [
            'itemCount' => $itemCount,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
}
