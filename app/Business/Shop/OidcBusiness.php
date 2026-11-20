<?php

namespace App\Business\Shop;

class OidcBusiness
{
    private string $domain = 'giggle-perennial-accustom.ngrok-free.dev';

    public function discovery(string $shopName)
    {
        return [
            'issuer' => "https://{$this->domain}/{$shopName}",
            'authorization_endpoint' => "https://{$this->domain}/{$shopName}/authorize",
            'token_endpoint' => "https://{$this->domain}/{$shopName}/token",
            'jwks_uri' => "https://{$this->domain}/{$shopName}/jwks.json",
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'scopes_supported' => ['openid', 'profile', 'email'],
            'claims_supported' => ["iss", "aud", "sub", "iat", "exp", "nonce", "email", "email_verified"],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post'],
            'code_challenge_methods_supported' => ['S256']
        ];
    }
    public function jwks()
    {
        $pem = file_get_contents(app_path('Ssl/Oidc/oidc-public.pem'));
        $rsa = openssl_pkey_get_details(openssl_pkey_get_public($pem));
        $b64 = fn ($v) => rtrim(strtr(base64_encode($v), '+/', '-_'), '=');
        return [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'alg' => 'RS256',
                    'kid' => 'oidc-rs256-2026-11',
                    'n' => $b64($rsa['rsa']['n']),
                    'e' => $b64($rsa['rsa']['e']),
                ]
            ],
        ];
    }
}