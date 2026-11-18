<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 建 5 张顾客分片表 customer_shard_0..4，字段名与 customer_hash_maps.table_name 对齐。
 *
 * 表必须落在 customer_hash_maps.database 指定的业务库（当前 v1 = old_six_us），
 * 不是默认的 old_six_admin；执行时用 DB_DATABASE 覆盖，例如：
 *   docker exec -w /var/www/html store-fpm sh -c 'DB_DATABASE=old_six_us php artisan migrate'
 * 这样 migrate 记录会写在业务库，old_six_admin 里那 35 条历史记录不受影响。
 *
 * id 是雪花 ID、由应用生成，所以不自增；Model 要设 public $incrementing = false。
 * created_at / updated_at 存 Unix 秒（项目 $dateFormat='U'），不用 $table->timestamps()。
 */
return new class extends Migration
{
    public function up(): void
    {
        for ($slot = 0; $slot < 5; $slot++) {
            Schema::create("customer_shard_{$slot}", function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();            // 雪花 ID，不自增；OIDC sub = (string)id
                $table->string('email')->unique();                      // 片内唯一，全局唯一由 email→id 索引层兜底
                $table->string('password')->nullable();                 // Shopify JSON 无此字段，IdP 验登录必需
                $table->unsignedInteger('email_verified_at')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone', 32)->nullable();                // 脏数据，不加唯一、不做 E.164 校验
                $table->string('state', 16)->default('ENABLED');        // 存 Shopify 原枚举，别压成 bool
                $table->string('shopify_gid')->nullable();
                $table->unsignedBigInteger('shopify_customer_id')->nullable()->index();
                $table->unsignedInteger('shopify_created_at')->nullable();
                $table->unsignedInteger('shopify_updated_at')->nullable()->index(); // 增量同步水位
                $table->string('email_marketing_state', 32)->nullable();
                $table->string('phone_marketing_state', 32)->nullable();
                $table->string('marketing_collected_from', 64)->nullable();
                $table->boolean('is_del')->default(0);
                $table->unsignedInteger('created_at')->nullable();
                $table->unsignedInteger('updated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        for ($slot = 0; $slot < 5; $slot++) {
            Schema::dropIfExists("customer_shard_{$slot}");
        }
    }
};
