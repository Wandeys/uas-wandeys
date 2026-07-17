<?php

namespace App\Observers;

use App\Models\Grade;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class GradeObserver
{
    public function created(Grade $grade): void
    {
        $nim = $grade->enrollment?->student?->nim ?? 'N/A';
        $className = $grade->enrollment?->class?->name ?? 'N/A';

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_GRADE',
            'description' => "Membuat nilai mahasiswa NIM: {$nim} di kelas: {$className}",
            'payload_before' => null,
            'payload_after' => $grade->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function updating(Grade $grade): void
    {
        if ($grade->isDirty()) {
            $nim = $grade->enrollment?->student?->nim ?? 'N/A';
            $className = $grade->enrollment?->class?->name ?? 'N/A';

            $desc = "Mengubah nilai mahasiswa NIM: {$nim} di kelas: {$className}";
            $action = 'UPDATE_GRADE';

            if ($grade->isDirty('is_locked') && $grade->is_locked) {
                $desc = "Finalisasi & Kunci nilai mahasiswa NIM: {$nim} di kelas: {$className}";
                $action = 'LOCK_GRADE';
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $desc,
                'payload_before' => array_intersect_key($grade->getOriginal(), $grade->getDirty()),
                'payload_after' => $grade->getDirty(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
