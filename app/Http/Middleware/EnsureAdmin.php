<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if (Route::has('login')) {
                return redirect()->guest(route('login'));
            }

            abort(401, 'Cần đăng nhập Admin. Khi test local: mở /dev-login/admin');
        }

        if (! $user->hasRole(User::ROLE_ADMIN)) {
            abort(403, 'Chỉ Admin được truy cập trang này.');
        }

        return $next($request);
    }
}
