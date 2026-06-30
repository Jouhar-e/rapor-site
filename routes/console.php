<?php

use App\Console\Commands\BackupRun;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup:run', function () {
    $this->call(BackupRun::class);
})->purpose('Run database backup');
