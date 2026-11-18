<?php

namespace App\Enums;

/**
 * 管理后台错误码 1010000-1099999
 * （10+xx+yyy）xx模块 yyy功能
 *
 * @method static static UNAUTHORIZED()
 * @method static static REGISTERED()
 */
final class ManageCode
{
    // 管理员模块 1010000-1010999
    public const ADMIN_LOGIN_PAGE_CLOSED = 1010000;
    public const ADMIN_LOGIN_TOKEN_EXPIRE = 1010001;
    public const ADMIN_UNABLE_AUTO_ASSIGN_PERMISSION = 1010002;
    public const ADMIN_INFO_NOT_FOUND = 1010003;
    public const MENU_PARENT_NOT_FOUND = 1010004;
    public const DATA_NO_FOUND = 1010005;
    public const MENU_NOT_FOUND = 1010006;
    public const PERMISSION_NOT_FOUND = 1010007;
    public const ROLE_NOT_FOUND = 1010008;
    public const ROOT_MENU_NOT_SUPPORT_DEL = 1010009;
    public const SLUG_REPEAT = 1010010;
    public const URI_NO_PERMISSION = 1010011;
    public const NAME_REPEAT = 1010012;
    public const USER_ROLE_NO_PERMISSION = 1010013;
    public const ADMIN_NOT_FOUND = 1010014;
    public const ADMIN_PASSWORD_ERROR = 1010015;
    public const ADMIN_EMAIL_EXISTS = 1010016;
    public const ADMIN_DISABLED = 1010017;
    public const PASSWORD_FORMAT_ERROR = 1010018;
    public const ADMIN_NEED_RESET_PASSWORD = 1010019;
    public const ADMIN_AUTH_TOO_MANY_ATTEMPTS = 1010020;
    public const FEISHU_OAUTH_STATE_INVALID = 1010021;
    public const FEISHU_GET_TOKEN_FAILED = 1010022;
    public const FEISHU_USER_INFO_FAILED = 1010023;
    public const MENU_HAS_CHILDREN = 1010024;


    // 翻译模块 1011000-1011999
    public const TRANSLATION_GROUP_IS_NOT_EXIST = 1011000;
    public const TRANSLATION_GROUP_ACTIVE_NOT_ALLOW_ARCHIVE = 1011001;
    public const TRANSLATION_KEY_CODE_EXIST = 1011002;
    public const TRANSLATION_KEY_IS_NOT_EXIST = 1011003;
    public const TRANSLATION_GROUP_IS_ARCHIVED = 1011004;
    public const TRANSLATION_GROUP_ARCHIVED_NOT_ALLOW_UPDATE = 1011005;
    public const TRANSLATION_KEY_NOT_ALLOW_UPDATE = 1011006;
    public const TRANSLATION_KEY_CREATE_FAIL = 1011007;
    public const TRANSLATION_KEY_UPDATE_FAIL = 1011008;
    public const TRANSLATION_KEY_CODE_ARCHIVED_EXIST = 1011009;
    public const TRANSLATION_KEY_ARCHIVED_NOT_ALLOW_UPDATE = 1011010;


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
            self::ADMIN_LOGIN_PAGE_CLOSED             => 'The login page has been closed',
            self::ADMIN_LOGIN_TOKEN_EXPIRE            => 'The login session has expired. Please log in again.',
            self::ADMIN_UNABLE_AUTO_ASSIGN_PERMISSION => 'Unable to automatically assign permissions for you. Please contact the administrator.',
            self::ADMIN_INFO_NOT_FOUND                => 'The server is busy. Please try again later.',
            self::MENU_PARENT_NOT_FOUND               => 'The parent menu does not exist.',
            self::DATA_NO_FOUND                       => 'The data does not exist.',
            self::MENU_NOT_FOUND                      => 'The menu does not exist.',
            self::PERMISSION_NOT_FOUND                => 'The permission %s does not exist.',
            self::ROLE_NOT_FOUND                      => 'The role %s does not exist.',
            self::ROOT_MENU_NOT_SUPPORT_DEL           => 'The root menu does not support deletion.',
            self::SLUG_REPEAT                         => 'The slug identifier is repeated.',
            self::URI_NO_PERMISSION                   => 'You don\'t have permission to access this site.. Please select a site where you have access.',
            self::NAME_REPEAT                         => 'The name is repeated.',
            self::ADMIN_NOT_FOUND                     => 'Incorrect account or password.',
            self::ADMIN_PASSWORD_ERROR                => 'Incorrect account or password.',
            self::ADMIN_EMAIL_EXISTS                  => 'The email already exists.',
            self::ADMIN_DISABLED                      => 'The account has been disabled.',
            self::PASSWORD_FORMAT_ERROR               => 'The password must be 6 to 50 characters long.',
            self::ADMIN_NEED_RESET_PASSWORD           => 'Please reset your password before logging in.',
            self::ADMIN_AUTH_TOO_MANY_ATTEMPTS        => 'Too many failed attempts. Please try again later.',
            self::FEISHU_OAUTH_STATE_INVALID          => 'The Feishu login request is invalid or has expired.',
            self::FEISHU_GET_TOKEN_FAILED             => 'Failed to obtain the Feishu access token.',
            self::FEISHU_USER_INFO_FAILED             => 'Failed to obtain Feishu user information.',
            self::MENU_HAS_CHILDREN                   => 'The menu has child menus and cannot be deleted.',

            // 翻译模块
            self::TRANSLATION_GROUP_IS_NOT_EXIST              => 'group is not exist',
            self::TRANSLATION_GROUP_ACTIVE_NOT_ALLOW_ARCHIVE  => 'Archiving the active resource group is not allowed.',
            self::TRANSLATION_KEY_CODE_EXIST                  => 'group key already exists.',
            self::TRANSLATION_KEY_IS_NOT_EXIST                => 'group key is not exist.',
            self::TRANSLATION_GROUP_IS_ARCHIVED               => 'The group is archived.',
            self::TRANSLATION_GROUP_ARCHIVED_NOT_ALLOW_UPDATE => 'Archived translation group cannot be modified.',
            self::TRANSLATION_KEY_NOT_ALLOW_UPDATE            => 'Archived translation group cannot be modify group key value',
            self::USER_ROLE_NO_PERMISSION                     => 'Can\'t submit since your roles are lower or different from others. Please contact the administrator.',
            self::TRANSLATION_KEY_CREATE_FAIL                  => 'Create group key value failed.',
            self::TRANSLATION_KEY_UPDATE_FAIL                  => 'Update group key value failed.',
            self::TRANSLATION_KEY_CODE_ARCHIVED_EXIST          => 'This key already exists and is archived. Please unarchive it before editing.',
            self::TRANSLATION_KEY_ARCHIVED_NOT_ALLOW_UPDATE    => 'Archived translation key cannot be modified.',
        ];
    }
}
