<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'quantity', 'reorder_point',
        'supplier_id', 'unit_id', 'cost_per_unit', 'last_restock_date', 'sku',
        'is_perishable', 'expiry_date', 'shelf_life_days', 'manufacture_date', 'batch_number'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'last_restock_date' => 'date',
        'is_perishable' => 'boolean',
        'cost_per_unit' => 'decimal:2',
        'quantity' => 'decimal:2',
        'reorder_point' => 'decimal:2',
    ];

    // Your existing relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // Add these helpful methods for coffee shop
    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out';
        } elseif ($this->quantity <= $this->reorder_point) {
            return 'low';
        } else {
            return 'safe';
        }
    }

    public function getStockPercentageAttribute()
    {
        if (!$this->reorder_point || $this->reorder_point == 0) {
            return 100;
        }
        return min(($this->quantity / $this->reorder_point) * 100, 100);
    }

    public function getInventoryValueAttribute()
    {
        return $this->quantity * $this->cost_per_unit;
    }

    // Expiry related methods
    public function getExpiryStatusAttribute()
    {
        if (!$this->is_perishable || !$this->expiry_date) {
            return 'non-perishable';
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry == 0) {
            return 'expires-today';
        } elseif ($daysUntilExpiry <= 3) {
            return 'expires-soon';
        } else {
            return 'safe';
        }
    }

    public function getExpiryStatusColorAttribute()
    {
        return [
            'non-perishable' => 'secondary',
            'safe' => 'success',
            'expires-soon' => 'warning',
            'expires-today' => 'danger',
            'expired' => 'dark'
        ][$this->expiry_status];
    }

    public function getExpiryStatusTextAttribute()
    {
        return [
            'non-perishable' => 'Non-Perishable',
            'safe' => 'Safe',
            'expires-soon' => 'Expires Soon',
            'expires-today' => 'Expires Today',
            'expired' => 'Expired'
        ][$this->expiry_status];
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->is_perishable || !$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }
}