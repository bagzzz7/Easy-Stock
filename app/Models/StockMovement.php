<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'medicine_id',
        'user_id',
        'supplier_id',
        'type',
        'reason',
        'quantity',
        'quantity_before',
        'quantity_after',
        'batch_number',
        'expiry_date',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_cost'   => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isStockIn(): bool
    {
        return $this->type === 'stock_in';
    }

    public function isStockOut(): bool
    {
        return $this->type === 'stock_out';
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'purchase'   => 'Purchase',
            'adjustment' => 'Manual Adjustment',
            'return'     => 'Return',
            'sale'       => 'Sale',
            'expired'    => 'Expired Disposal',
            'damaged'    => 'Damaged / Lost',
            'transfer'   => 'Transfer Out',
            default      => ucfirst($this->reason),
        };
    }
}