<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function updating(User $user): void
    {
        if ($user->isDirty('password')) {
            AuditLog::create([
                'user_id' => Auth::id() ?? $user->id,
                'action' => 'CHANGE_PASSWORD',
                'description' => 'Mengubah password user: ' . $user->email,
                'payload_before' => null,
                'payload_after' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public function deleted(User $user): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_USER',
            'description' => 'Menghapus user: ' . $user->email . ' (Nama: ' . $user->name . ', Role: ' . $user->role . ')',
            'payload_before' => $user->toArray(),
            'payload_after' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
