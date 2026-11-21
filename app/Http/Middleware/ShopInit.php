<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Repositories\Manage\ShopRepository;
use App\Exceptions\CommonException;
use App\Helpers\ShopHelper;
use Symfony\Component\HttpFoundation\Response;

class ShopInit
{
    protected ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $shopName = $request->header('X-Mmos-Shop');
        $availableShops = $this->shopRepository->getPlainShopList();
        $currentShop = $availableShops->where('shop_name', $shopName)->where('status', 1)->first();
        if (empty($currentShop)) {
            throw new CommonException(
                'SHOP_NOT_FOUND',
                'Shop not found',
                Response::HTTP_NOT_FOUND
            );
        }
        ShopHelper::init($currentShop);

        return $next($request);
    }
}
