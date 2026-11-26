<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code'    => 401,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.',
                    'errors'  => null,
                ], 401);
            }
            return redirect()->route('login');
        }

        $normalizedRoles = array_map(
            static fn (string $r): string => strtolower(str_replace(' ', '_', $r)),
            $roles
        );
        $userRole = strtolower((string) ($user->role ?? ''));

        if (!in_array($userRole, $normalizedRoles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code'    => 403,
                    'message' => 'Bạn không có quyền truy cập chức năng này.',
                    'errors'  => null,
                ], 403);
            }
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}