<?php
namespace Civi\Micro\Sql;

class NotFoundException extends \RuntimeException {
    public function __construct(\Exception $ex) {
        parent::__construct($ex);
    }
}