<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_amount',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'notes'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function addItem($medicineId, $quantity, $unitPrice)
    {
        return $this->items()->create([
            'medicine_id' => $medicineId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice
        ]);
    }
}