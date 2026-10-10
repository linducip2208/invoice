<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model);
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        // Skip noise
        unset($changes['updated_at'], $changes['last_used_at'], $changes['last_check_at']);
        if (empty($changes)) return;

        $this->log('updated', $model, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model);
    }

    protected function log(string $action, Model $model, ?array $changes = null): void
    {
        $workspaceId = $model->workspace_id ?? workspace()?->id;
        if (! $workspaceId) return;

        try {
            AuditLog::create([
                'workspace_id' => $workspaceId,
                'user_id'      => auth()->id(),
                'action'       => $model->getTable() . '.' . $action,
                'subject_type' => get_class($model),
                'subject_id'   => $model->getKey(),
                'description'  => sprintf('%s %s #%s', ucfirst($action), class_basename($model), $model->getKey()),
                'changes'      => $changes,
                'ip'           => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Don't break the main operation if audit log fails
            logger()->warning('Audit log failed: ' . $e->getMessage());
        }
    }
}
