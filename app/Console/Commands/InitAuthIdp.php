<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ShopHelper;
use App\Repositories\Manage\ShopRepository;
use App\Repositories\Shop\AuthIdpConfigsRepository;
use App\Models\Manage\ShopModel;
use Illuminate\Support\Str;
use Illuminate\Database\UniqueConstraintViolationException;
use Throwable;

class InitAuthIdp extends Command
{
    protected $signature = 'auth:init-idp
        {shopName : shop name, e.g. shop1}';

    protected $description = 'Initialize an auth_idp_configs record by shop name';

    public function handle(
        ShopRepository $shopRepository,
        AuthIdpConfigsRepository $authIdpConfigsRepository
    ): int
    {
        $shopName = $this->argument('shopName');
        $shopList = $shopRepository->getPlainShopList();
        $shop = $shopList->first(fn($shop) => $shop->shop_name === $shopName);
        if (empty($shop)) {
            $this->error("Shop not found: {$shopName}");
            return self::FAILURE;
        }
        ShopHelper::init($shop);
        $callbackUris = $this->buildIdpRedirectUris($shop);
        $loginRedirectUris = $callbackUris['login_redirect_uris'];
        $logoutRedirectUris = $callbackUris['logout_redirect_uris'];
        try {
            $authIdpConfigsRepository->create([
                'idp_name' => $shopName . '_idp',
                'client_id' => Str::uuid()->toString(),
                'client_secret' => Str::random(64),
                'login_redirect_uris' => $loginRedirectUris,
                'logout_redirect_uris' => $logoutRedirectUris,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $this->error("IdP config exists：{$shopName}_idp");
            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error("Failed to create IdP config：{$shopName}");
            return self::FAILURE;
        }
        $this->info("IdP config created successfully：{$shopName}_idp");
        echo 'Idp created successfully. Please check the database for the new record.' . PHP_EOL;
        return self::SUCCESS;
    }

    private function buildIdpRedirectUris(ShopModel $shop): array
    {
        $shopHost = parse_url($shop->shop_domain, PHP_URL_HOST);
        $shopId = basename($shop->shopify_gid);
        $shopHandle = str_replace('.myshopify.com', '', $shopHost);
        $accountHost = "{$shopHandle}.account.myshopify.com";

        return [
            'login_redirect_uris' => [
                "https://shopify.com/authentication/{$shopId}/login/external/callback",
                "https://{$accountHost}/authentication/login/external/callback",
            ],
            'logout_redirect_uris' => [
                "https://{$shopHost}/customer_authentication/logout/callback",
                "https://shopify.com/{$shopId}/account/logout_callback",
                "https://{$accountHost}/logout_callback",
            ],
        ];
    }
}


