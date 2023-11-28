<?php
namespace Civi\Micro\Impl\Sql;

use PDO;
use Civi\Micro\Enviroment;
use Civi\Micro\SpecificConfig;
use Ray\Di\InjectionPointInterface;
use Ray\Di\ProviderInterface;

class SqlTemplateBuilder implements ProviderInterface {
    public function __construct(private readonly InjectionPointInterface $ip, private readonly Enviroment $env) {
    }
    public function get() {
        $env = SpecificConfig::env('datasource', $this->ip, $this->env);
        $pdo = new PDO($env->property('url'), $env->property('username'), $env->property('password'), [PDO::ATTR_PERSISTENT => true] );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return new SqlTemplateImpl( $pdo );
    }
}