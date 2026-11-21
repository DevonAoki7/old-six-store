<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof CommonException) {
            return response()->json([
                'code' => $exception->getErrorCode(),
                'message' => $exception->getMessage(),
                'data' => $exception->getData(),
            ], $exception->getStatusCode(), $exception->getHeaders());
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'data' => [
                    'errors' => $exception->errors(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 异常自带 render() 时交给它自己渲染（如 OidcException：
        // authorize 错误重定向、token 错误 JSON），避免在此堆 instanceof 分支
        if (method_exists($exception, 'render')) {
            return $exception->render();
        }

        return parent::render($request, $exception);
    }
}
