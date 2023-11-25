<?php
namespace Civi\Micro\Impl;

use Civi\Micro\Enviroment;

class EnviromentImpl implements Enviroment {
    public function getProperty(string $name): string {
        return 'value = ' . $name;
    }
}