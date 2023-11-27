<?php
namespace Civi\Micro\Jwt;

class TokenVerificationInfo {
    // TODO: iis, y otros
    public function __construct(public readonly string $jwks) {
    }
}