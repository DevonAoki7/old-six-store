<?php

namespace App\Repositories\Manage;

use App\Repositories\BaseRepository;
use App\Models\Manage\ShopModel;
use Illuminate\Support\Facades\Cache;
use App\Constants\CacheKeyConstant;
use \Illuminate\Support\Collection;

class ShopRepository extends BaseRepository
{
    public function __construct(ShopModel $ShopModel)
    {
        $this->model = $ShopModel;
    }

    public function getShopNameList()
    {
        return Cache::remember(
            CacheKeyConstant::MANAGE_SHOPS_PLAIN_NAME,
            now()->addDay(),
            fn() => $this->model->query()
                ->where('status', 1)
                ->pluck('shop_name')
        );
    }

    public function getPlainShopList()
    {
        return $this->model
            ->query()
            ->with('shopMenus')
            ->where('status', 1)
            ->get();
    }

    public function getShopList(): Collection
    {
        return Cache::remember(
            CacheKeyConstant::MANAGE_SHOPS,
            now()->addDay(),
            fn() => $this->model->query()
                ->with(['shopCountries' => function ($query) {
                    $query->orderBy('sort', 'asc');
                }])->orderBy('sort')->get()
        );
    }
}
