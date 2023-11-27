<?php
use Civi\Micro\Context;
use Civi\Micro\Impl\Sql\PdoBuilder;
use Civi\Micro\Impl\EnviromentImpl;
use Civi\Micro\Sql\DataSource;
use Civi\Micro\Enviroment;
use Civi\Micro\Jwt\TokenVerifier;
use Civi\Micro\Jwt\TokenSigner;
use Civi\Micro\Jwt\TokenHandler;

return function(Context $context) {
    // Entorno
    $context->bind(Enviroment::class)->to(EnviromentImpl::class);
    // Base de datos.
    $context->bind(PDO::class)->toProvider(PdoBuilder::class);
    // Jwt
    $context->bind(TokenVerifier::class)->to(TokenHandler::class);
    $context->bind(TokenSigner::class)->to(TokenHandler::class);
};