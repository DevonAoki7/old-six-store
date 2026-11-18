<?php

namespace App\Models\Manage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopMenuModel extends Model
{
    protected $table = 'shop_menus';

    public function menus(): BelongsTo
    {
        return $this->belongsTo(MenuModel::class, 'menu_id', 'id');
    }
}
