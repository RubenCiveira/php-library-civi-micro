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
        $token = $this->getToken( $request );
        if( $token ) {            
            $jwksUri = 'http://localhost/dev/back/ejemplo-micro-uno/public/.well-known/jwks.json';
            $jwksJson = file_get_contents($jwksUri);
            $isVerified = $this->verifier->verify($token, new TokenVerificationInfo(jwks: $jwksJson));
            var_dump( $isVerified );
        }
        return $request;
    }

    private function getToken(Request $request) {
        $auth = $request->getHeader('Authorization');
        if( $auth && str_starts_with( $auth[0], 'Bearer ') ) {
            return substr($auth[0], 7);
        }
    }

}

// Esta clase se ejecutará al inicio de la aplicación