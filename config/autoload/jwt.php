<?php
declare(strict_types=1);

return [
    'login_type'             => env('JWT_LOGIN_TYPE', 'mpop'),
    'sso_key'                => '',
    'secret'                 => env('JWT_SECRET', 'cqykpy'),
    'keys'                   => [
        'public'  => env('JWT_PUBLIC_KEY'),
        'private' => env('JWT_PRIVATE_KEY'),
    ],
    'ttl'                    => env('JWT_TTL', 7200),
    'alg'                    => env('JWT_ALG', 'HS256'),
    'supported_algs'         => [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
        'ES256' => 'Lcobucci\JWT\Signer\Ecdsa\Sha256',
        'ES384' => 'Lcobucci\JWT\Signer\Ecdsa\Sha384',
        'ES512' => 'Lcobucci\JWT\Signer\Ecdsa\Sha512',
        'RS256' => 'Lcobucci\JWT\Signer\Rsa\Sha256',
        'RS384' => 'Lcobucci\JWT\Signer\Rsa\Sha384',
        'RS512' => 'Lcobucci\JWT\Signer\Rsa\Sha512',
    ],
    'symmetry_algs'          => [
        'HS256',
        'HS384',
        'HS512'
    ],
    'asymmetric_algs'        => [
        'RS256',
        'RS384',
        'RS512',
        'ES256',
        'ES384',
        'ES512',
    ],
    'blacklist_enabled'      => env('JWT_BLACKLIST_ENABLED', false),
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),
    'blacklist_cache_ttl'    => env('JWT_TTL', 86400),
    'blacklist_prefix'       => 'cqykpy',
    'scene'                  => [
        'default' => [],
    ],
    'model'                  => [
        'class' => '',
        'pk'    => ''
    ]
];
