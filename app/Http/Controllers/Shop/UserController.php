<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 登录接口
     */
    public function login(Request $request)
    {
        // TODO: 登录逻辑
    }

    /**
     * 检查邮箱是否已注册
     */
    public function checkEmail(Request $request)
    {
        return response()->json(['email' => '']);
    }
}
