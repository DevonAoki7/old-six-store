<?php

namespace App\Business\Shop;

use App\Enums\OidcCode;
use App\Exceptions\OidcException;
use App\Repositories\Manage\ShopRepository;
use App\Repositories\Shop\AuthIdpConfigsRepository;
use App\Helpers\ShopHelper;
use Symfony\Component\HttpFoundation\Response;

class OidcAuthBusiness
{
    private ShopRepository $shopRepository;
    private AuthIdpConfigsRepository $authIdpConfigsRepository;

    public function __construct(
        ShopRepository $shopRepository,
        AuthIdpConfigsRepository $authIdpConfigsRepository
    )
    {
        $this->shopRepository = $shopRepository;
        $this->authIdpConfigsRepository = $authIdpConfigsRepository;
    }

    // 前端发起授权请求，shopify 会重定向浏览器到这里
    public function authorize($params)
    {
        [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
            'state' => $state,
            'nonce' => $nonce,
        ] = $params;
        // 路由是 /{shop}/authorize，controller 注入的键是 shop；兼容旧调用方的 shop_name
        $shopName = $params['shop_name'] ?? $params['shop'] ?? null;

        // 1. shop 必须存在且启用
        // 用 getPlainShopList 而不是 find：ShopRepository::find 声明返回 ShopModel，
        // 查不到时 first() 返回 null 会触发 TypeError，empty() 分支永远走不到
        $shop = $this->shopRepository->getPlainShopList()
            ->where('shop_name', $shopName)
            ->where('status', 1)
            ->first();
        if (empty($shop)) {
            // 此时还不知道合法 redirect_uri，只能返回 JSON error，不能重定向
            throw new OidcException(
                OidcCode::INVALID_REQUEST,
                'Shop not found or disabled: ' . (string) $shopName,
                Response::HTTP_BAD_REQUEST
            );
        }
        ShopHelper::init($shop); // 切到该店铺业务库，auth_idp_configs 在 mysql_shop 上

        // 2. client_id 必须能对应到该店的 IdP 配置（存在且可用）
        $idpConfig = $this->authIdpConfigsRepository->findByAttr(['client_id' => $clientId]);
        if (empty($idpConfig)) {
            throw new OidcException(
                OidcCode::UNAUTHORIZED_CLIENT,
                'IdP config not found for client_id',
                Response::HTTP_BAD_REQUEST
            );
        }

        // 3. redirect_uri 必须在注册的白名单内
        // model 已 casts 两个字段为 array；严格比较避免类型混淆
        $allowedUris = array_merge(
            $idpConfig->login_redirect_uris ?? [],
            $idpConfig->logout_redirect_uris ?? []
        );
        if (! in_array($redirectUri, $allowedUris, true)) {
            // RFC 6749 §4.1.2.1：redirect_uri 无效时禁止重定向到它，直接返回 error
            throw new OidcException(
                OidcCode::INVALID_REQUEST,
                'redirect_uri is not registered for this client',
                Response::HTTP_BAD_REQUEST
            );
        }

        // redirect_uri 已验证，后续错误可以带它重定向，state 原样回传
        if ($responseType !== 'code') {
            throw new OidcException(
                OidcCode::UNSUPPORTED_RESPONSE_TYPE,
                'response_type must be code',
                Response::HTTP_BAD_REQUEST,
                $redirectUri,
                $state
            );
        }

        return [];
    }
}
