<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ตรวจสอบว่า user login แล้วหรือไม่
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = auth()->user();

        // ตรวจสอบว่าเป็น admin หรือ SA หรือไม่
        if (!$user->hasAdminAccess() && !$user->isSuperAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ เฉพาะ Admin หรือ Super Admin เท่านั้น');
        }

        return $next($request);
    }
}
