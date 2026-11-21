<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof Response) {
            $response = response()->json($response);
        }

        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return $response;
        }

        if ($response->isRedirection() || in_array($response->getStatusCode(), [204, 304], true)) {
            return $response;
        }

        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $content = $response->getData(true);

        if (is_array($content)
            && array_key_exists('code', $content)
            && array_key_exists('message', $content)) {
            return $response;
        }

        $response->setData([
            'code' => 0,
            'data' => $content,
            'message' => null,
        ]);

        return $response;
    }
}
