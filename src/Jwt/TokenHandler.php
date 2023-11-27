<?php
namespace Civi\Micro\Jwt;

use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;

class TokenHandler implements TokenVerifier, TokenSigner  {

    public function createKeyPair(): KeyPair {
        $privateKeyResource = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);
        // Exportar claves
        openssl_pkey_export($privateKeyResource, $privateKey);
        $publicKey = openssl_pkey_get_details($privateKeyResource)['key'];
        return new KeyPair(privateKey: $privateKey, publicKey: $publicKey);
    }

    public function sign(array $indentity, PrivateKey $key): string {
        $privateKeyContent = JWKFactory::createFromKey( $key->key );
        // Crear el gestor de algoritmos con RS256
        $algorithmManager = new AlgorithmManager([new RS256()]);

        // Crear un JWT Builder
        $jwsBuilder = new JWSBuilder($algorithmManager);

        // Payload del JWT
        $payload = json_encode([
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + 3600, // Token expira en 1 hora
                'sub' => 'tu-subject',
                // ... otros campos del payload
        ]);
        // Construir y firmar el token
        $token = $jwsBuilder->create() // Crear el JWS
                    ->withPayload($payload) // Establecer el payload
                    ->addSignature($privateKeyContent, ['alg' => 'RS256']) // Añadir la firma
                    ->build();

         // Serializar el token a formato compacto
        $serializer = new CompactSerializer();
        return $serializer->serialize($token, 0);
    }

    /**
     *  @param PublicKey[] $elementos Array de objetos de tipo MiClase
     */
    public function convertToJwks(array $publics): string {
        $keys = [];
        foreach($publics as $pk) {
            $keys[] = JWKFactory::createFromKey($pk->key);
        }
        $jwkSet = new JWKSet($keys);
        return json_encode($jwkSet);
    }

    public function verify(string $token, TokenVerificationInfo $info) {
        $jwkSet = JWKSet::createFromJson($info->jwks);
        $serializer = new CompactSerializer();
        try {
            $jwt = $serializer->unserialize($token);
        } catch(\InvalidArgumentException $je) {
            $jwt = null;
        }
        $isVerified = false;
        if( $jwt ) {
            // Crear un verificador de JWS con el algoritmo correspondiente
            $algorithmManager = new AlgorithmManager([new RS256()]);
            $jwsVerifier = new JWSVerifier($algorithmManager);
            // Verificar el token con el JWKSet
            $isVerified = $jwsVerifier->verifyWithKeySet($jwt, $jwkSet, 0);
        }
        return $isVerified;
    }

}