<?php
use Civi\Micro\Context;
use Civi\Micro\Impl\Sql\PdoBuilder;
use Civi\Micro\Impl\Sql\SqlTemplateBuilder;
use Civi\Micro\Impl\EnviromentImpl;
use Civi\Micro\Sql\SqlTemplate;
use Civi\Micro\Enviroment;
use Civi\Micro\Jwt\TokenVerifier;
use Civi\Micro\Jwt\TokenSigner;
use Civi\Micro\Jwt\TokenHandler;
use Civi\Micro\Migration\MigrationService;

return function(Context $context) {
    // Entorno
    $context->bind(Enviroment::class)->to(EnviromentImpl::class);
    // Base de datos.
    // Deprecado
    $context->bind(PDO::class)->toProvider(PdoBuilder::class);
    $context->bind(SqlTemplate::class)->toProvider(SqlTemplateBuilder::class);
    // Jwt
    $context->bind(TokenVerifier::class)->to(TokenHandler::class);
    $context->bind(TokenSigner::class)->to(TokenHandler::class);
    // Migrations
    $context->bind(MigrationService::class);
};