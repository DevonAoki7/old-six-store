<?php

namespace App\Exceptions;

use App\Enums\OidcCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

/**
 * OIDC 认证链路异常：承载协议规定的 error 码，交给 Handler 统一渲染。
 *
 * 错误码与默认 error_description 定义在 App\Enums\OidcCode，本类不重复声明。
 *
 * 两类码：
 *   协议错误（校验失败，属预期结果）—— OidcCode::INVALID_REQUEST /
 *     UNAUTHORIZED_CLIENT / UNSUPPORTED_RESPONSE_TYPE / ACCESS_DENIED 等。
 *   服务端故障（真正意外）—— OidcCode::SERVER_ERROR / TEMPORARILY_UNAVAILABLE。
 *
 * redirectUri / state 仅用于 authorize 阶段的错误重定向：
 * 只有 redirect_uri 已通过白名单校验才传；否则留 null，Handler 返回 JSON error。
 *
 * message 是内部诊断信息，只进日志；回给 RP 的 error_description 默认取
 * OidcCode::getDescription(errorCode)，除非构造时显式覆盖。
 */
class OidcException extends RuntimeException
{
    /**
     * @param  string  $errorCode  取值见 OidcCode 常量，原样写入响应 error 字段
     * @param  string  $message  内部诊断信息，只进日志，不回给 Shopify
     * @param  int|null  $httpStatus  响应状态码；authorize 校验失败统一 400
     * @param  string|null  $redirectUri  已通过白名单校验的回调地址；有值则重定向
     * @param  string|null  $state  请求携带的 state，重定向时须原样回传
     * @param  string|null  $errorDescription  覆盖默认的 error_description；null 则取 OidcCode
     */
    public function __construct(
        public readonly string $errorCode,
        string $message = '',
        public readonly ?int $httpStatus = null,
        public readonly ?string $redirectUri = null,
        public readonly ?string $state = null,
        public readonly ?string $errorDescription = null,
    ) {
        parent::__construct($message);
    }

    /** 回给 RP 的描述：显式传入优先，否则取 Enum 默认 */
    public function getErrorDescription(): string
    {
        return $this->errorDescription ?? OidcCode::getDescription($this->errorCode);
    }

    /**
     * 自己渲染响应，Handler 用 method_exists($e, 'render') 统一委托。
     *
     * 有 redirectUri（authorize 阶段且 uri 已过白名单）→ 302 附 error 参数；
     * 否则（token 阶段或 uri 无效）→ JSON error body。
     * message 只进日志，不回给 RP；error_description 取 Enum 或显式覆盖值。
     */
    public function render(): JsonResponse|RedirectResponse
    {
        if ($this->redirectUri !== null) {
            $query = [
                'error'             => $this->errorCode,
                'error_description' => $this->getErrorDescription(),
            ];
            if ($this->state !== null) {
                $query['state'] = $this->state; // 请求带 state 必须原样回传
            }
            $separator = str_contains($this->redirectUri, '?') ? '&' : '?';

            return redirect()->away($this->redirectUri . $separator . http_build_query($query));
        }

        return response()->json([
            'error'             => $this->errorCode,
            'error_description' => $this->getErrorDescription(),
        ], $this->httpStatus ?? 400, ['Cache-Control' => 'no-store']);
    }
}
