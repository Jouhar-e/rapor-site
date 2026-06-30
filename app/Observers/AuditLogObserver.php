<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogObserver
{
    public function created(Model $model): void
    {
        $this->log('create', $model);
    }

    public function updated(Model $model): void
    {
        $this->log('update', $model);
    }

    public function deleted(Model $model): void
    {
        $this->log('delete', $model);
    }

    protected function log(string $action, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $oldValues = match ($action) {
            'update' => $model->getOriginal(),
            'delete' => $model->getAttributes(),
            default => null,
        };

        $newValues = match ($action) {
            'create' => $model->getAttributes(),
            'update' => $model->getChanges(),
            default => null,
        };

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
