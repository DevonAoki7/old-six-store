<?php

namespace App\Enums;

/**
 * @method static static UNAUTHORIZED()
 * @method static static REGISTERED()
 */
final class CommonCode
{
    //公共错误码
    public const INVALID_ARGUMENT_ERROR = 42000;
    public const RECORD_NOT_FOUND = 42001;
    public const UNAUTHENTICATED = 401;
    public const TOKEN_EXPIRED = 401;
    public const INVALID_CREDENTIALS = 42007;
    public const TOO_MANY_REQUESTS = 42008;
    public const MMOS_SHOP_ERROR = 42009;
    public const MMOS_CLIENT_ERROR = 42010;
    public const MMOS_SHOP_COUNTRY_ERROR = 42012;
    public const INTERNAL_ERROR = 50000;
    public const SYSTEM_ERROR = 50001;
    public const THIRD_PARTY_API_ERROR = 43000;
    public const MMOS_APP_ERROR = 42011;
    public const MMOS_APP_AUTH_ERROR = 420101;
    public const DATE_EMPTY = 43001;
    public const FORBIDDEN_ONLY_TEST = 403;

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
            self::INVALID_ARGUMENT_ERROR => 'INVALID ARGUMENT ERROR',
            self::RECORD_NOT_FOUND => 'RECORD NOT FOUND',
            self::UNAUTHENTICATED => 'UNAUTHENTICATED',
            self::INVALID_CREDENTIALS => 'INVALID CREDENTIALS',
            self::TOO_MANY_REQUESTS => 'TOO MANY REQUESTS',
            self::MMOS_SHOP_ERROR => __('common.mmos_shop_error'),
            self::MMOS_CLIENT_ERROR => __('common.mmos_client_error'),
            self::MMOS_SHOP_COUNTRY_ERROR => __('common.mmos_shop_country_error'),
            self::INTERNAL_ERROR => 'INTERNAL ERROR',
            self::SYSTEM_ERROR => 'SYSTEM ERROR',
            self::THIRD_PARTY_API_ERROR => 'THIRD PARTY API ERROR',
//            self::TOKEN_EXPIRED => __('common.token_has_expired'),
            self::MMOS_APP_ERROR => __('common.mmos_app_error'),
            self::MMOS_APP_AUTH_ERROR => __('common.mmos_app_auth_error'),
            self::DATE_EMPTY => __('common.date_empty'),
            self::FORBIDDEN_ONLY_TEST => 'Forbidden by production'
        ];
    }
}
