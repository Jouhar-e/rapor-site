<?php

namespace App\Models;

use Database\Factories\BackupHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['filename', 'file_size', 'type', 'status', 'started_at', 'completed_at', 'notes'])]
class BackupHistory extends Model
{
    /** @use HasFactory<BackupHistoryFactory> */
    use HasFactory;
}
