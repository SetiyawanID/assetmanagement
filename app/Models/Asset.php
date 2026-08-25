<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag', 'name', 'category_id', 'location_id', 'assigned_to',
        'brand', 'model', 'serial_number', 'purchase_date', 'purchase_price',
        'status', 'condition', 'warranty_until', 'notes',
    ];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'warranty_until' => 'date', 'purchase_price' => 'decimal:2'];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
}
