<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
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

        // ตรวจสอบว่าเป็น SA user หรือไม่
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ เฉพาะ Super Admin เท่านั้น');
        }

        return $next($request);
    }
}
