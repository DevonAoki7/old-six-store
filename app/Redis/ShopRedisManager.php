<?php

namespace App\Redis;

use App\Helpers\ShopHelper;
use Illuminate\Support\Facades\Redis;

class ShopRedisManager
{
    /**
     * 按当前店铺给 key 加前缀，避免多店共用一个 Redis 实例时串 key。
     */
    private function key(string $key): string
    {
        $shop = ShopHelper::getShop();
        if (! $shop) {
            throw new \LogicException('Shop context not initialized');
        }

        return $shop->shop_name . ':' . $key;
    }

    /**
     * 按店铺区域选择连接，EU 店走独立实例。
     * redis_eu 尚未在 config/database.php 定义，配置补齐前先回落 default。
     */
    private function connection()
    {
        $shop = ShopHelper::getShop();
        $name = $shop->is_eu == 1 && config('database.redis.redis_eu') ? 'redis_eu' : 'default';

        return Redis::connection($name);
    }

    /**
     * 写入带过期时间的字符串。
     */
    public function set(string $key, string $value, int $ttl): void
    {
        $this->connection()->setex($this->key($key), $ttl, $value);
    }

    /**
     * 读取；key 不存在返回 null。
     */
    public function get(string $key): ?string
    {
        $value = $this->connection()->get($this->key($key));

        return $value === false ? null : $value;
    }

    /**
     * 原子地读取并删除；key 不存在返回 null。
     */
    public function getDel(string $key): ?string
    {
        $value = $this->connection()->getDel($this->key($key));

        return $value === false ? null : $value;
    }

    /**
     * key 不存在时才写入，返回是否写入成功；可用于去重或简单互斥。
     */
    public function setNx(string $key, string $value, int $ttl): bool
    {
        return (bool) $this->connection()->set($this->key($key), $value, 'EX', $ttl, 'NX');
    }

    /**
     * 删除 key。
     */
    public function del(string $key): void
    {
        $this->connection()->del($this->key($key));
    }
}
