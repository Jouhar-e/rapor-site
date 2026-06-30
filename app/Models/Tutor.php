<?php

namespace App\Models;

use Database\Factories\TutorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'nip',
    'name',
    'gender',
    'birth_place',
    'birth_date',
    'address',
    'phone',
    'email',
    'photo',
    'is_active',
])]
class Tutor extends Model
{
    /** @use HasFactory<TutorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Tutor $tutor) {
            HomeroomTeacher::where('user_id', $tutor->user_id)->delete();
        });

        static::deleted(function (Tutor $tutor) {
            $tutor->user?->delete();
        });
    }
}
