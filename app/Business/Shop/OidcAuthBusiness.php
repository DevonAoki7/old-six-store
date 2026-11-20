<?php

namespace App\Business\Shop;

use App\Repositories\Manage\ShopRepository;
use App\Repositories\Shop\AuthIdpConfigsRepository;
use App\Helpers\ShopHelper;

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
    public function authorize($params)
    {
        [
            'shop_name' => $shopName,
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
            'state' => $state,
            'nonce' => $nonce,
        ] = $params;
        $shop = $this->shopRepository->find(['shop_name' => $shopName]);
        if (empty($shop)) {
            throw new \Exception('Shop not found');
        }
        ShopHelper::init($shop);
        return [];
    }   
}