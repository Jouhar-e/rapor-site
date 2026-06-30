<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        app(AuditService::class)->logLogin();
    }
}
