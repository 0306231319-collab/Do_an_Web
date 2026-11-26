<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'code'    => 422,
                        'message' => 'Dữ liệu đầu vào không hợp lệ.',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'code'    => 401,
                        'message' => 'Chưa xác thực hoặc phiên đăng nhập hết hạn.',
                        'errors'  => null,
                    ], 401);
                }

                if ($e instanceof AccessDeniedHttpException) {
                    return response()->json([
                        'success' => false,
                        'code'    => 403,
                        'message' => $e->getMessage() ?: 'Không có quyền thực hiện thao tác này.',
                        'errors'  => null,
                    ], 403);
                }

                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'code'    => 404,
                        'message' => 'Không tìm thấy tài nguyên yêu cầu.',
                        'errors'  => null,
                    ], 404);
                }

                $status = $e instanceof HttpException ? $e->getStatusCode() : 500;

                return response()->json([
                    'success' => false,
                    'code'    => $status,
                    'message' => $e->getMessage() ?: 'Lỗi hệ thống máy chủ.',
                    'errors'  => null,
                ], $status);
            }

            return null;
        });
    })->create();