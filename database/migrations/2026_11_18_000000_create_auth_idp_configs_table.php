<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 该表需要创建在 old_six_us 业务库。
 *
 * 执行方式：
 *   docker exec -w /var/www/html store-fpm sh -c 'DB_DATABASE=old_six_us php artisan migrate'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_idp_configs', function (Blueprint $table) {
            $table->id();
            $table->string('idp_name', 100);
            $table->string('client_id', 250);
            $table->string('client_secret', 250);
            $table->string('login_redirect_uri', 500);
            $table->string('logout_redirect_uri', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_idp_configs');
    }
};
