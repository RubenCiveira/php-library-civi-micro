<?php
namespace Civi\Micro\Impl\Sql;

use PDO;
use Civi\Micro\Enviroment;
use Civi\Micro\SpecificConfig;
use Ray\Di\InjectionPointInterface;
use Ray\Di\ProviderInterface;


class PdoBuilder  implements ProviderInterface {
    public function __construct(private readonly InjectionPointInterface $ip, private readonly Enviroment $env) {
    }
    public function get() {
        $env = SpecificConfig::env($this->ip, $this->env);
        $pdo = new PDO($env->property('url'), $env->property('username'), $env->property('password') );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}