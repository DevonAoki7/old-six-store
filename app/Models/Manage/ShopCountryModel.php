<?php

namespace App\Models\Manage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopCountryModel extends Model
{
    use HasFactory;

    protected $table = 'shop_countries';

    // created_at / updated_at 是 int 存 Unix 秒，不设的话 Eloquent 会按 'Y-m-d H:i:s' 写入，STRICT_TRANS_TABLES 下报 1265。
    protected $dateFormat = 'U';

    public const STATUS_ABLE = 1;

    public const STATUS_UNABLE = 0;

    public const STATUS_DELETE = 2;
}
