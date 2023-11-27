<?php
namespace Civi\Micro\Jwt;

interface TokenVerifier {
    public function verify(string $token, TokenVerificationInfo $info);
}