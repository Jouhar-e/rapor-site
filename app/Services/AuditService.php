<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function logCreate(Model $model): AuditLog
    {
        return $this->log(
            action: 'create',
            modelType: get_class($model),
            modelId: $model->getKey(),
            newValues: $model->toArray(),
        );
    }

    public function logUpdate(Model $model, array $original): AuditLog
    {
        return $this->log(
            action: 'update',
            modelType: get_class($model),
            modelId: $model->getKey(),
            oldValues: $original,
            newValues: $model->getDirty(),
        );
    }

    public function logDelete(Model $model): AuditLog
    {
        return $this->log(
            action: 'delete',
            modelType: get_class($model),
            modelId: $model->getKey(),
            oldValues: $model->toArray(),
        );
    }

    public function logLogin(): AuditLog
    {
        return $this->log('login');
    }

    public function logLogout(): AuditLog
    {
        return $this->log('logout');
    }

    public function logImport(string $type, int $imported, int $updated, int $skipped, ?array $errors = null): AuditLog
    {
        return $this->log(
            action: 'import',
            newValues: [
                'type' => $type,
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
                'has_errors' => ! empty($errors),
            ],
        );
    }

    public function logExport(string $type, ?array $filters = null): AuditLog
    {
        return $this->log(
            action: 'export',
            newValues: [
                'type' => $type,
                'filters' => $filters,
            ],
        );
    }

    public function logPublish(string $modelType, ?int $modelId = null): AuditLog
    {
        return $this->log('publish', $modelType, $modelId);
    }

    public function logLock(string $modelType, ?int $modelId = null): AuditLog
    {
        return $this->log('lock', $modelType, $modelId);
    }

    public function logPromotion(string $action, array $data): AuditLog
    {
        return $this->log('promotion', newValues: array_merge(['action' => $action], $data));
    }

    public function logBackup(string $action, ?string $filename = null): AuditLog
    {
        return $this->log('backup', newValues: [
            'action' => $action,
            'filename' => $filename,
        ]);
    }
}
