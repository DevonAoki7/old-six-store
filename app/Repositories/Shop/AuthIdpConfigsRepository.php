<?php

namespace App\Repositories\Shop;

use App\Models\Shop\AuthIdpConfigsModel;

class AuthIdpConfigsRepository
{
    protected AuthIdpConfigsModel $authIdpConfigsModel;
    public function __construct(AuthIdpConfigsModel $authIdpConfigsModel)
    {
        $this->authIdpConfigsModel = $authIdpConfigsModel;
    }
    public function create(array $data): AuthIdpConfigsModel
    {
        return $this->authIdpConfigsModel->create($data);
    }

    public function findByAttr(array $attr): ?AuthIdpConfigsModel
    {
        return $this->authIdpConfigsModel->where($attr)->first();
    }
}