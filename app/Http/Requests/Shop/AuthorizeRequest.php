<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;

class AuthorizeRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'client_id' => ['required', 'string'],
            'redirect_uri' => ['required', 'url'],
            'response_type' => ['required', 'in:code'],
            'scope' => ['required', 'string'],
            'state' => ['required', 'string'],
            'nonce' => ['required', 'string'],
            'code_challenge' => ['nullable', 'string'],
            'code_challenge_method' => ['nullable', 'string'],
        ];
    }
}