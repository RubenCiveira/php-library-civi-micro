<?php
namespace Civi\Micro\Jwt;

interface TokenSigner {

    public function createKeyPair(): KeyPair;

    public function sign(array $indentity, PrivateKey $key): string;

    /**
     *  @param PublicKey[] $elementos Array de objetos de tipo MiClase
     */
    public function convertToJwks(array $publics);
}