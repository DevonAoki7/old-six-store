<?php

namespace App\Business\Shop;

use App\Enums\OidcCode;
use App\Exceptions\OidcException;
use App\Repositories\Shop\AuthIdpConfigsRepository;
use App\Helpers\ShopHelper;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;

class OidcAuthBusiness
{
    /** Shopify 只会请求 openid + email；profile 可选，其余一律拒绝 */
    private const SUPPORTED_SCOPES = ['openid', 'email', 'profile'];

    private AuthIdpConfigsRepository $authIdpConfigsRepository;

    public function __construct(AuthIdpConfigsRepository $authIdpConfigsRepository)
    {
        $this->authIdpConfigsRepository = $authIdpConfigsRepository;
    }

    // 前端发起授权请求，shopify 会重定向浏览器到这里
    public function authorize(array $params)
    {
        [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
            'state' => $state,
            'fullUrl' => $fullUrl,
        ] = $params;
        // AuthorizeRequest 里这两项是 nullable，未传时 validated() 不含 key
        $codeChallenge       = $params['code_challenge'] ?? null;
        $codeChallengeMethod = $params['code_challenge_method'] ?? null;
        // shop 由 ShopInitOidc 中间件校验并初始化（从 /{shop}/... 路径参数取），
        // 这里直接从上下文取，auth_idp_configs 已切到该店业务库。
        $shop = ShopHelper::getShop();

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
        if (! in_array($redirectUri, $idpConfig->login_redirect_uris, true)) {
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
        // 4. scope：空格分隔，必须含 openid，其余项必须在支持列表内
        $this->validateScopes($scope, $redirectUri, $state);

        // 5. PKCE：method 必须与 discovery 声明一致；challenge 格式合法
        $this->validatePkce($codeChallenge, $codeChallengeMethod, $redirectUri, $state);

        // 6. 跳登录页：state 带加密后的完整 authorize URL（mmos 方案）。
        //    登录成功后 login 接口解密还原本 URL、追加 token 再跳回 authorize 第二趟，
        //    Shopify 的原始参数（state/nonce/code_challenge）都还在 URL 里，无需 Redis 中转。
        return redirect()->away($shop->primary_domain . '/auth/shop/login?' . http_build_query([
            'state' => Crypt::encrypt($fullUrl),
        ]));
    }

    /**
     * OIDC Core §3.1.2.1：必须含 openid；其余 scope 必须在 SUPPORTED_SCOPES 内。
     * redirect_uri 已过白名单，校验失败重定向回 Shopify 并原样回传 state。
     */
    private function validateScopes(mixed $scope, string $redirectUri, string $state): void
    {
        $scopes = is_string($scope)
            ? array_values(array_filter(preg_split('/\s+/', trim($scope)) ?: [], fn ($item) => $item !== ''))
            : [];

        if (! in_array('openid', $scopes, true)) {
            throw new OidcException(
                OidcCode::INVALID_SCOPE,
                'scope must contain openid',
                Response::HTTP_BAD_REQUEST,
                $redirectUri,
                $state
            );
        }

        foreach ($scopes as $item) {
            if (! in_array($item, self::SUPPORTED_SCOPES, true)) {
                throw new OidcException(
                    OidcCode::INVALID_SCOPE,
                    'unsupported scope: ' . $item,
                    Response::HTTP_BAD_REQUEST,
                    $redirectUri,
                    $state
                );
            }
        }
    }

    /**
     * RFC 7636：challenge 与 method 必须成对出现，method 只允许 discovery 声明的 S256，
     * challenge 是 base64url(SHA256(verifier))，43~128 字符、无 padding。
     * 真正的比对在 token 端点用 code_verifier 算一遍再 hash_equals。
     */
    private function validatePkce(?string $challenge, ?string $method, string $redirectUri, string $state): void
    {
        if ($challenge === null && $method === null) {
            return;
        }

        if ($challenge === null || $method === null) {
            throw new OidcException(
                OidcCode::INVALID_REQUEST,
                'code_challenge and code_challenge_method must appear together',
                Response::HTTP_BAD_REQUEST,
                $redirectUri,
                $state
            );
        }

        if ($method !== 'S256') {
            throw new OidcException(
                OidcCode::INVALID_REQUEST,
                'unsupported code_challenge_method',
                Response::HTTP_BAD_REQUEST,
                $redirectUri,
                $state
            );
        }

        if (! preg_match('/^[A-Za-z0-9\-._~]{43,128}$/', $challenge)) {
            throw new OidcException(
                OidcCode::INVALID_REQUEST,
                'invalid code_challenge format',
                Response::HTTP_BAD_REQUEST,
                $redirectUri,
                $state
            );
        }
    }
}
