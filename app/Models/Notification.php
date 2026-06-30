<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Cast;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'])]
#[Cast('read_at', 'datetime')]
#[Cast('data', 'array')]
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;
}
