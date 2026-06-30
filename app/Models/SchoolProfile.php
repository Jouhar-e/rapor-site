<?php

namespace App\Models;

use Database\Factories\SchoolProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'npsn',
    'address',
    'district',
    'city',
    'province',
    'postal_code',
    'phone',
    'email',
    'website',
    'logo',
    'headmaster_name',
    'headmaster_nip',
    'headmaster_signature',
    'school_stamp',
    'description',
])]
class SchoolProfile extends Model
{
    /** @use HasFactory<SchoolProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
