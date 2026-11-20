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
        $prarms = $request->validated();
        $prarms['shop'] = $request->route('shop');
        $response = $this->oidcAuthBusiness->authorize($prarms);
        return response()->json($response);
    }
}