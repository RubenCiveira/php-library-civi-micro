<?php
namespace Civi\Micro\Sql;

interface SqlTemplate {
    public function execute($query, array $params): bool ;

    public function query($query, array $params, Clousure $clousure): array;

    public function findOne($query, array $param, Clousure $clousure);

    public function exists($query, array $param): bool;

}