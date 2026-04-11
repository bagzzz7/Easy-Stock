<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'generic_name', 
        'brand', 
        'description', 
        'category_id',
        'category_type',        // adults, children, both
        'unit',                 // tablet, capsule, ml, mg, g, mcg, unit
        'strength',             // e.g., 500, 250 (without unit)
        'supplier_id', 
        'purchase_price', 
        'selling_price', 
        'quantity',
        'reorder_level', 
        'expiry_date', 
        'batch_number', 
        'shelf_number',
        'requires_prescription', 
        'status'
    ];

    protected $dates = ['expiry_date'];

    protected $casts = [
        'expiry_date' => 'date',
        'requires_prescription' => 'boolean'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // ============ STATUS METHODS ============
    
    /**
     * Update medicine status based on quantity and expiry
     */
    public function updateStatus()
    {
        if ($this->expiry_date < now()) {
            $this->status = 'expired';
        } elseif ($this->quantity <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
        
        $this->saveQuietly();
    }

    /**
     * Check if medicine is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * Check if medicine is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Check if medicine is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    /**
     * Get days until expiry (negative if expired)
     */
    public function daysUntilExpiry(): int
    {
        return (int) now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get days since expiry (positive if expired)
     */
    public function daysSinceExpiry(): int
    {
        return $this->isExpired() 
            ? (int) $this->expiry_date->diffInDays(now()) 
            : 0;
    }

    // ============ UNIT MEASUREMENT METHODS ============

    /**
     * Get the unit display name
     */
    public function getUnitDisplayAttribute(): string
    {
        $units = [
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'ml' => 'mL',
            'mg' => 'mg',
            'g' => 'g',
            'mcg' => 'mcg',
            'unit' => 'Unit',
            'ampule' => 'Ampule',
            'vial' => 'Vial',
            'sachet' => 'Sachet',
            'bottle' => 'Bottle',
            'tube' => 'Tube'
        ];

        return $units[$this->unit] ?? ucfirst($this->unit);
    }

    /**
     * Get the unit plural form
     */
    public function getUnitPluralAttribute(): string
    {
        $plurals = [
            'tablet' => 'tablets',
            'capsule' => 'capsules',
            'ml' => 'mL',
            'mg' => 'mg',
            'g' => 'g',
            'mcg' => 'mcg',
            'unit' => 'units',
            'ampule' => 'ampules',
            'vial' => 'vials',
            'sachet' => 'sachets',
            'bottle' => 'bottles',
            'tube' => 'tubes'
        ];

        return $plurals[$this->unit] ?? $this->unit . 's';
    }

    /**
     * Get complete strength with unit
     */
    public function getFullStrengthAttribute(): ?string
    {
        if ($this->strength) {
            return $this->strength . ' ' . $this->unit_display;
        }
        return $this->unit_display;
    }

    /**
     * Get formatted quantity with unit
     */
    public function getQuantityWithUnitAttribute(): string
    {
        return $this->quantity . ' ' . ($this->quantity == 1 ? $this->unit_display : $this->unit_plural);
    }

    /**
     * Get reorder level with unit
     */
    public function getReorderLevelWithUnitAttribute(): string
    {
        return $this->reorder_level . ' ' . ($this->reorder_level == 1 ? $this->unit_display : $this->unit_plural);
    }

    /**
     * Get all available unit options for select dropdowns
     */
    public static function getUnitOptions(): array
    {
        return [
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'ml' => 'Milliliter (mL)',
            'mg' => 'Milligram (mg)',
            'g' => 'Gram (g)',
            'mcg' => 'Microgram (mcg)',
            'unit' => 'Unit',
            'ampule' => 'Ampule',
            'vial' => 'Vial',
            'sachet' => 'Sachet',
            'bottle' => 'Bottle',
            'tube' => 'Tube'
        ];
    }

    // ============ ACCESSORS ============

    /**
     * Get formatted strength with unit
     */
    public function getFormattedStrengthAttribute(): ?string
    {
        if ($this->strength) {
            return $this->strength . ' ' . $this->unit_display;
        }
        return $this->unit_display;
    }

    /**
     * Get unit symbol for display
     */
    public function getUnitSymbolAttribute(): string
    {
        return $this->getUnitSymbol();
    }

    /**
     * Get category type badge HTML
     */
    public function getCategoryTypeBadgeAttribute(): string
    {
        $badges = [
            'adults' => '<span class="badge bg-primary">Adults Only</span>',
            'children' => '<span class="badge bg-success">Children Only</span>',
            'both' => '<span class="badge bg-info">Adults & Children</span>'
        ];

        return $badges[$this->category_type] ?? '<span class="badge bg-secondary">Not Specified</span>';
    }

    /**
     * Get formatted selling price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->selling_price, 2);
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price > 0) {
            return round((($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
        }
        return 0;
    }

    /**
     * Get profit amount
     */
    public function getProfitAmountAttribute(): float
    {
        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'in_stock' => 'success',
            'low_stock' => 'warning',
            'out_of_stock' => 'secondary',
            'expired' => 'danger'
        ];
        
        $color = $colors[$this->status] ?? 'info';
        $label = str_replace('_', ' ', ucfirst($this->status));
        
        return "<span class=\"badge bg-{$color}\">{$label}</span>";
    }

    /**
     * Get prescription badge HTML
     */
    public function getPrescriptionBadgeAttribute(): string
    {
        return $this->requires_prescription 
            ? '<span class="badge bg-danger">Rx Required</span>'
            : '<span class="badge bg-success">OTC</span>';
    }

    /**
     * Get expiry status badge HTML
     */
    public function getExpiryBadgeAttribute(): string
    {
        if ($this->isExpired()) {
            return '<span class="badge bg-danger">Expired</span>';
        }
        
        $daysLeft = $this->daysUntilExpiry();
        
        if ($daysLeft <= 30) {
            return '<span class="badge bg-warning">Expiring Soon</span>';
        }
        
        return '<span class="badge bg-success">Valid</span>';
    }

    // ============ PRIVATE METHODS ============

    /**
     * Get unit symbol for display (private helper)
     */
    private function getUnitSymbol(): string
    {
        $symbols = [
            'tablet' => 'tablet(s)',
            'capsule' => 'capsule(s)',
            'ml' => 'mL',
            'mg' => 'mg',
            'g' => 'g',
            'mcg' => 'mcg',
            'unit' => 'unit(s)'
        ];

        return $symbols[$this->unit] ?? $this->unit ?? '';
    }

    // ============ SCOPES ============

    /**
     * Scope for in stock medicines
     */
    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    /**
     * Scope for low stock medicines
     */
    public function scopeLowStock($query)
    {
        return $query->where('status', 'low_stock');
    }

    /**
     * Scope for out of stock medicines
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
    }

    /**
     * Scope for expired medicines
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for medicines expiring soon
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '>', now())
                     ->where('expiry_date', '<=', now()->addDays($days))
                     ->where('status', '!=', 'expired');
    }

    /**
     * Scope for medicines by category type
     */
    public function scopeForAdults($query)
    {
        return $query->whereIn('category_type', ['adults', 'both']);
    }

    public function scopeForChildren($query)
    {
        return $query->whereIn('category_type', ['children', 'both']);
    }

    /**
     * Scope for prescription medicines
     */
    public function scopeRequiresPrescription($query)
    {
        return $query->where('requires_prescription', true);
    }

    /**
     * Scope for OTC medicines
     */
    public function scopeOverTheCounter($query)
    {
        return $query->where('requires_prescription', false);
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('generic_name', 'LIKE', "%{$term}%")
              ->orWhere('brand', 'LIKE', "%{$term}%")
              ->orWhere('batch_number', 'LIKE', "%{$term}%")
              ->orWhere('strength', 'LIKE', "%{$term}%");
        });
    }

    // ============ BOOT METHOD ============

    /**
     * Boot method to auto-update status on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($medicine) {
            // Auto-update status before saving
            if ($medicine->expiry_date < now()) {
                $medicine->status = 'expired';
            } elseif ($medicine->quantity <= 0) {
                $medicine->status = 'out_of_stock';
            } elseif ($medicine->quantity <= $medicine->reorder_level) {
                $medicine->status = 'low_stock';
            } else {
                $medicine->status = 'in_stock';
            }
        });

        // Update status when retrieving from database
        static::retrieved(function ($medicine) {
            $medicine->updateStatus();
        });
    }
}