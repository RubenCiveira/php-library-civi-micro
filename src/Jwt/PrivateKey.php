<?php
namespace Civi\Micro\Jwt;

class PrivateKey {
    public function __construct(public readonly string $key) {
    }
}