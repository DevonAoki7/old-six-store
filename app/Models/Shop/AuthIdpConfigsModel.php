<?php

namespace App\Models\Shop;

use App\Models\BaseShopModel;

class AuthIdpConfigsModel extends BaseShopModel
{
    protected $table = 'auth_idp_configs';

    protected $fillable = [
        'idp_name',
        'client_id',
        'client_secret',
        'login_redirect_uris',
        'logout_redirect_uris',
    ];

    protected $hidden = [
        'client_secret',
    ];

    protected $casts = [
        'login_redirect_uris' => 'array',
        'logout_redirect_uris' => 'array',
    ];
}