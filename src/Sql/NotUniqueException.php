<?php
namespace Civi\Micro\Sql;

class NotUniqueException extends \RuntimeException {
    public function __construct(\Exception $ex) {
        parent::__construct($ex);
    }
}