<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['name', 'slug', 'color'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'status', 'slug');
    }
}
