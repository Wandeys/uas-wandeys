<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogObserver
{
    public function deleted($model): void
    {
        $modelName = class_basename($model);
        $action = 'DELETE_' . strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));

        $desc = "Menghapus {$modelName} ID: {$model->id}";
        if (isset($model->name)) {
            $desc .= " (Nama: {$model->name})";
        } elseif (isset($model->code)) {
            $desc .= " (Kode: {$model->code})";
        } elseif (isset($model->year)) {
            $desc .= " (Tahun: {$model->year} - {$model->semester})";
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $desc,
            'payload_before' => $model->toArray(),
            'payload_after' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
