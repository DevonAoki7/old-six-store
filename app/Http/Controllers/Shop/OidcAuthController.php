<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use App\Http\Requests\Shop\AuthorizeRequest;
use App\Business\Shop\OidcAuthBusiness;

class OidcAuthController extends Controller
{
    private OidcAuthBusiness $oidcAuthBusiness;
    public function __construct(OidcAuthBusiness $oidcAuthBusiness)
    {
        $this->oidcAuthBusiness = $oidcAuthBusiness;
    }
    public function authorize(AuthorizeRequest $request)
    {
        $params = $request->validated();
        $params['shop'] = $request->route('shop');
        $params['fullUrl'] = $request->fullUrl();
        return $this->oidcAuthBusiness->authorize($params);
    }
}