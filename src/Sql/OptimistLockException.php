<?php
namespace Civi\Micro\Sql;

class OptimistLockException extends \RuntimeException {
    public function __construct(\Exception $ex) {
        parent::__construct($ex);
    }
}