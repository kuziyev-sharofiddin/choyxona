<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
        'status'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function isReturned()
    {
        return $this->status === 'returned';
    }

    public function canBeReturned()
    {
        return in_array($this->status, ['ready', 'served']) &&
            $this->order->status !== 'completed';
    }

    // Scope for returned items
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
}
