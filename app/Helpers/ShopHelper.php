<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Manage\ShopModel;

class ShopHelper
{
    public static ShopModel $shop;

    public static function init(ShopModel $shop)
    {
        self::$shop = $shop;
        DB::purge('mysql_shop');
        $baseDbConfig = null;
        if ($shop->is_eu == 1) {
            $baseDbConfig = config('database.connections.mysql_eu');
        } else {
            $baseDbConfig = config('database.connections.mysql');
        }
        if (isset($baseDbConfig)) {
            $baseDbConfig['write']['database'] = $shop->database;
            $baseDbConfig['read']['database'] = $shop->database;
            // 第一行是设置 php 语言时区，第二行是 laravel 框架的时区
            // 正常 laravel 启动会加载 config 里的时区调用 date_default_timezone_set 设置
            // 如果定时任务等一个进程启动设置不同库数据，则不会调用，会出现时区错乱
            Config::set('database.connections.mysql_shop', $baseDbConfig);
            date_default_timezone_set($shop->timezone);
            Config::set('app.timezone', $shop->timezone);
        }
    }

    public static function getShop()
    {
        return self::$shop;
    }
}
