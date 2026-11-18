<?php

namespace App\Constants;

class CacheKeyConstant
{
    public const MANAGE_SHOPS = 'manage_shops';
    public const MANAGE_SHOPS_PLAIN_NAME = 'manage_shops_plain_name';

    public const MANAGE_ADMIN = 'manage_admin';
    public const MANAGE_USER_ROLES = 'manage_user_roles';
    public const MANAGE_FULL_MENUS = 'manage_full_menus';

    public const VERIFICATION_CODE = 'verification_code';
    //找回密码Token
    public const USER_PASSWORD_RESET_TOKEN = 'user_password_reset_token';

    public static function getUserPasswordResetToken($email)
    {
        return self::USER_PASSWORD_RESET_TOKEN . ':' . md5($email);
    }
}
