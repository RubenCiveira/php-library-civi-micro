<?php
namespace Civi\Micro\Jwt;

class PublicKey {
    public function __construct(public readonly string $key) {
    }
}