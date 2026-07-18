<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'type', 'file_name', 'total_rows', 'imported',
    'updated', 'skipped', 'errors', 'created_by',
])]
class ImportHistory extends Model
{
    protected function casts(): array
    {
        return [
            'errors' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
