<?php
namespace Civi\Micro\Filter;

use Civi\Micro\RequestFilter;
use Civi\Micro\Jwt\TokenVerifier;
use Civi\Micro\Jwt\TokenVerificationInfo;

use Psr\Http\Message\ServerRequestInterface as Request;


class OAuthFilter implements RequestFilter {
    // Tiene que leer la infor d
    public function __construct(private readonly TokenVerifier $verifier) {
    }

    public function filter(Request $request): Request {
        if( strpos($request->getUri()->getPath(), '.well-known') === false ) {
            $key = 'http://localhost/dev/back/ejemplo-micro-uno/public/.well-known/sign.json';
            $token = file_get_contents($key);
            
            $jwksUri = 'http://localhost/dev/back/ejemplo-micro-uno/public/.well-known/jwks.json';
            $jwksJson = file_get_contents($jwksUri);
            $isVerified = $this->verifier->verify($token, new TokenVerificationInfo(jwks: $jwksJson));
            var_dump( $isVerified );
        }
        return $request;
    }
}

// Esta clase se ejecutará al inicio de la aplicación