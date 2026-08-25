<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag', 'barcode', 'name', 'category_id', 'location_id', 'assigned_to',
        'brand', 'model', 'serial_number', 'purchase_date', 'purchase_price',
        'status', 'condition', 'warranty_until', 'notes',
    ];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'warranty_until' => 'date', 'purchase_price' => 'decimal:2'];
    }

    protected static function booted(): void
    {
        static::creating(function (Asset $asset): void {
            if ($asset->barcode) {
                return;
            }

            do {
                $barcode = 'ASTBC-'.Str::upper(Str::random(12));
            } while (static::where('barcode', $barcode)->exists());

            $asset->barcode = $barcode;
        });
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function statusDefinition(): BelongsTo { return $this->belongsTo(Status::class, 'status', 'slug'); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
}
