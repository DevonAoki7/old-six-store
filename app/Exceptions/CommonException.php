<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CommonException extends HttpException
{
    protected int|string $errorCode;

    protected mixed $data;

    public function __construct(
        int|string $errorCode = 'COMMON_ERROR',
        string $message = 'Request failed',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $data = null,
        ?Throwable $previous = null,
        array $headers = []
    ) {
        $this->errorCode = $errorCode;
        $this->data = $data;

        parent::__construct(
            $statusCode,
            $message,
            $previous,
            $headers,
            is_int($errorCode) ? $errorCode : 0
        );
    }

    public function getErrorCode(): int|string
    {
        return $this->errorCode;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
