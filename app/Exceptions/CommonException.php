<?php

namespace App\Exceptions;

use App\Enums\ManageCode;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommonException extends HttpException
{
    public function __construct(
        int $code,
        $message = '',
        $statusCode = ResponseAlias::HTTP_OK,
        \Exception $previous = null,
        array $headers = []
    ) {
        $message = empty($message) ? ManageCode::getDescription($code) : $message;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
