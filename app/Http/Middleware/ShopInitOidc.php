<?php

namespace App\Http\Middleware;

use App\Enums\OidcCode;
use App\Exceptions\OidcException;
use App\Helpers\ShopHelper;
use App\Repositories\Manage\ShopRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OIDC 路由的店铺初始化：shop 来自 URL 路径 /{shop}/...（Shopify 重定向不带 X-Mmos-Shop 头）。
 * 校验店铺存在且启用，并把上下文切到该店业务库。
 */
class ShopInitOidc
{
    protected ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function handle(Request $request, Closure $next)
    {
        $shopName = $request->route('shop');

        // 用 getPlainShopList 而不是 find：find 声明返回 ShopModel，
        // 查不到时 first() 返回 null 会触发 TypeError，empty() 分支永远走不到。
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

        ShopHelper::init($shop);

        return $next($request);
    }
}
