<?php

namespace App\Enums;

/**
 * OIDC / OAuth2 协议错误码与默认描述。
 *
 * const 的值就是协议规定的 error 字符串，会原样写进响应的 error 字段，
 * 不能自造、不能改动。descriptions() 给的是默认 error_description，
 * 面向 RP 开发者、ASCII 可读；带变量的内部诊断信息不写在这里。
 *
 * Authorization endpoint: RFC 6749 §4.1.2.1 + OIDC Core §3.1.2.6
 * Token endpoint:         RFC 6749 §5.2
 */
final class OidcCode
{
    // ---- Authorization endpoint ----
    public const INVALID_REQUEST           = 'invalid_request';
    public const UNAUTHORIZED_CLIENT       = 'unauthorized_client';
    public const ACCESS_DENIED             = 'access_denied';
    public const UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';
    public const INVALID_SCOPE             = 'invalid_scope';
    public const LOGIN_REQUIRED            = 'login_required';
    public const INTERACTION_REQUIRED      = 'interaction_required';
    public const SERVER_ERROR              = 'server_error';
    public const TEMPORARILY_UNAVAILABLE   = 'temporarily_unavailable';

    // ---- Token endpoint ----
    public const INVALID_CLIENT          = 'invalid_client';
    public const INVALID_GRANT           = 'invalid_grant';
    public const UNSUPPORTED_GRANT_TYPE  = 'unsupported_grant_type';

    public static function getDescription($value): string
    {
        $descriptions = self::descriptions();
        if (isset($descriptions[$value])) {
            return $descriptions[$value];
        }

        return '';
    }

    public static function descriptions(): array
    {
        return [
            self::INVALID_REQUEST           => 'The request is missing a required parameter or is otherwise malformed.',
            self::UNAUTHORIZED_CLIENT       => 'The client is not authorized to use this authorization method.',
            self::ACCESS_DENIED             => 'The resource owner or authorization server denied the request.',
            self::UNSUPPORTED_RESPONSE_TYPE => 'The authorization server does not support the requested response type.',
            self::INVALID_SCOPE             => 'The requested scope is invalid, unknown, or malformed.',
            self::LOGIN_REQUIRED            => 'The end-user must authenticate before the request can be completed.',
            self::INTERACTION_REQUIRED      => 'The authorization server requires end-user interaction.',
            self::SERVER_ERROR              => 'The authorization server encountered an unexpected condition.',
            self::TEMPORARILY_UNAVAILABLE   => 'The authorization server is temporarily unavailable.',
            self::INVALID_CLIENT            => 'Client authentication failed.',
            self::INVALID_GRANT             => 'The authorization grant or refresh token is invalid or expired.',
            self::UNSUPPORTED_GRANT_TYPE    => 'The authorization grant type is not supported.',
        ];
    }
}
