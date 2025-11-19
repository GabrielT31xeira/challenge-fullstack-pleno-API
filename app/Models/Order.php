<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'total',
        'subtotal',
        'tax',
        'shipping_cost',
        'shipping_address',
        'billing_address',
        'notes',
        'cart_id'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'total'            => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'tax'              => 'decimal:2',
        'shipping_cost'    => 'decimal:2',
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
