<?php
use Civi\Micro\Context;
use Civi\Micro\Impl\Sql\DataSourceImpl;
use Civi\Micro\Impl\EnviromentImpl;
use Civi\Micro\Sql\DataSource;
use Civi\Micro\Enviroment;

return function(Context $context) {
    $context->bind(DataSource::class)->to(DataSourceImpl::class);
    $context->bind(Enviroment::class)->to(EnviromentImpl::class);
    // OAuth en maria db
    $context->bind(Civi\Micro\OAuth\Persistence::class)->to(Civi\Micro\Impl\OAuth\PersistenceSql::class);
};