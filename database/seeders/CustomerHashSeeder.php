<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerHashSeeder extends Seeder
{
    /**
     * 灌一套最简分片路由配置：1 条 config + 5 条 map（slot 0~4）。
     *
     * 路由链路：雪花 id -> 命中 [min_id, max_id) 的 version -> hash32(id) % shard_num
     *          -> customer_hash_maps(version, hash_value) -> (database, table_name)
     * hash_value 存的是取模后的槽位，不是原始哈希值；hash32 的实现必须与路由代码保持一致。
     *
     * database / table_name 是占位值：$database 取本机实际存在的业务库 old_six_us，
     * 与 shops.database 的 justfab-us 对齐；其余 5 个站点库尚未创建。
     * 落库位置或分片表名定了改下面两个变量，重跑本 seeder 会覆盖。
     */
    public function run(): void
    {
        $version     = 'v1';
        $shardNum    = 5;
        $database    = 'old_six_us';
        $tablePrefix = 'customer_shard_';
        $now         = time(); // created_at / updated_at 是 int unsigned 存 Unix 秒，不是 datetime

        DB::table('customer_hash_configs')->upsert(
            [[
                'version'    => $version,
                'min_id'     => null, // NULL = 无界；半开区间 [min_id, max_id)
                'max_id'     => null,
                'shard_num'  => $shardNum,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['version'],
            ['min_id', 'max_id', 'shard_num', 'updated_at']
        );

        $rows = [];
        for ($slot = 0; $slot < $shardNum; $slot++) {
            $rows[] = [
                'version'    => $version,
                'hash_value' => $slot, // 槽位 = hash32(id) % shard_num
                'table_name' => $tablePrefix . $slot,
                'database'   => $database,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('customer_hash_maps')->upsert(
            $rows,
            ['version', 'hash_value'],
            ['table_name', 'database', 'updated_at']
        );
    }
}
