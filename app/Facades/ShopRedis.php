<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void  set(string $key, string $value, int $ttl)
 * @method static ?string get(string $key)
 * @method static ?string getDel(string $key)
 * @method static bool  setNx(string $key, string $value, int $ttl)
 * @method static void  del(string $key)
 *
 * @see \App\Redis\ShopRedisManager
 */
class ShopRedis extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Redis\ShopRedisManager::class;
    }
}
