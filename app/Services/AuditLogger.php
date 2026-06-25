<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function log(string $action, string $module, ?string $description = null): void
    {
        $user = request()->user();

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'system',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function login(): void
    {
        $user = request()->user();
        if ($user) {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        }
        static::log('login', 'auth', 'User logged in');
    }

    public static function logout(): void
    {
        static::log('logout', 'auth', 'User logged out');
    }
}
