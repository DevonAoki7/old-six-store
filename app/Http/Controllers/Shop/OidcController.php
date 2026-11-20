<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use App\Business\Shop\OidcBusiness;

class OidcController extends Controller
{
    public OidcBusiness $oidcBusiness;

    public function __construct(OidcBusiness $oidcBusiness)
    {
        $this->oidcBusiness = $oidcBusiness;
    }
    public function discovery()
    {
        $shopName = request()->route('shop');
        return response()->json($this->oidcBusiness->discovery($shopName));
    }
    public function jwks()
    {
        return response()->json($this->oidcBusiness->jwks());
    }
}
