<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRequest extends Model
{
    protected $fillable = ['type', 'action', 'target_id', 'payload', 'requested_by', 'reviewed_by', 'status', 'reviewed_at', 'read_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'reviewed_at' => 'datetime', 'read_at' => 'datetime'];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public static function pendingForTarget(string $type, int $targetId): ?self
    {
        return static::pending()
            ->where('type', $type)
            ->where('target_id', $targetId)
            ->with('requester')
            ->latest()
            ->first();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'category' => 'Kategori',
            'status' => 'Status',
            'user' => 'Akun User',
            default => ucfirst($this->type),
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Tambah',
            'update' => 'Edit',
            'delete' => 'Hapus',
            default => ucfirst($this->action),
        };
    }
}
