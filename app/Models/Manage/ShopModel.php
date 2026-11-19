<?php

namespace App\Models\Manage;

use Illuminate\Database\Eloquent\Model;

class ShopModel extends Model
{
    protected $table = 'shops';

    // created_at / updated_at 是 int 存 Unix 秒，不设置会按 'Y-m-d H:i:s' 写入
    protected $dateFormat = 'U';

    public function shopCountries()
    {
        return $this->hasMany(ShopCountryModel::class, 'shop_name', 'shop_name');
    }
}
