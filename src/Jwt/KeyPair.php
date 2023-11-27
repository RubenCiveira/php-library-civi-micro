<?php
namespace Civi\Micro\Jwt;

class KeyPair {
    public function __construct(public readonly string $privateKey, public readonly string $publicKey) {
    }
}