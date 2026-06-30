<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        app(AuditService::class)->logLogout();
    }
}
