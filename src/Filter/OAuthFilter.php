<?php
namespace Civi\Micro\Filter;

use Civi\Micro\RequestFilter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;

class OAuthFilter implements RequestFilter {

    public function filter(Request $request): Request {
        if( strpos($request->getUri()->getPath(), '.well-known') === false ) { 
            $key = 'http://localhost/dev/back/ejemplo-micro-uno/public/.well-known/sign.json';
            $token = file_get_contents($key);
           //  echo "<h1>TT: " . $token ."</p>";
            $jwksUri = 'http://localhost/dev/back/ejemplo-micro-uno/public/.well-known/jwks.json';
            $jwksJson = file_get_contents($jwksUri);
            $jwkSet = JWKSet::createFromJson($jwksJson);

            // $jwkSet = json_decode($jwksJson, true);
            // print_r( $jwkSet );

            $serializer = new CompactSerializer();
            $jwt = $serializer->unserialize($token);

            // Crear un verificador de JWS con el algoritmo correspondiente
            $algorithmManager = new AlgorithmManager([new RS256()]);
            $jwsVerifier = new JWSVerifier($algorithmManager);

            // Verificar el token con el JWKSet
            $isVerified = $jwsVerifier->verifyWithKeySet($jwt, $jwkSet, 0);
            var_dump( $isVerified );
        }
        // Tiene que evaluar cabeceras y toda la pesca
        // echo "<h4>Filtro de oauth para saber si tiene usuario</h4>";
        return $request;
    }
}

// Esta clase se ejecutará al inicio de la aplicación