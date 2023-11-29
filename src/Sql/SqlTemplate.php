<?php
namespace Civi\Micro\Sql;

use Closure;

interface SqlTemplate {
    public function execute($query, array $params): bool ;

    public function query($query, array $params, Closure $clousure): array;

    public function findOne($query, array $param, Closure $clousure);

    public function exists($query, array $param): bool;

}