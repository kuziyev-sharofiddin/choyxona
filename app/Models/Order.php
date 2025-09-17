<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'reservation_id',
        'customer_id',
        'waiter_id',
        'customer_phone',
        'customer_name',
        'delivery_address',
        'delivery_fee',
        'subtotal',
        'order_type',
        'tax_amount',
        'waiter_commission',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'order_time',
        'served_time'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'waiter_commission' => 'decimal:2', // Add this
        'total_amount' => 'decimal:2',
        'order_time' => 'datetime',
        'served_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $order->order_time = now();
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal()
    {
        $this->subtotal = $this->items()->whereNotIn('status', ['returned'])->sum('total_price');
        $this->tax_amount = 0; // No tax

        // Use config for commission rate
        $commissionRate = config('choyxona.waiter_commission_rate', 0.10);

        $this->waiter_commission = $this->order_type === 'dine_in' ? $this->subtotal * $commissionRate : 0;
        $additionalFee = $this->order_type === 'delivery' ? $this->delivery_fee : 0;

        $this->total_amount = $this->subtotal + $additionalFee + $this->waiter_commission - $this->discount_amount;
        $this->save();
    }
    public function needsReservation()
    {
        return $this->order_type === 'dine_in';
    }
    public function getOrderTypeDisplayAttribute()
    {
        $types = [
            'dine_in' => 'Ichkarida ovqatlanish',
            'takeaway' => 'Olib ketish',
            'delivery' => 'Yetkazib berish'
        ];

        return $types[$this->order_type] ?? $this->order_type;
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaid()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getRemainingAmount()
    {
        return $this->total_amount - $this->getTotalPaid();
    }

    public function isFullyPaid()
    {
        return $this->getRemainingAmount() <= 0;
    }
}
