<?php
namespace Civi\Micro;

interface Enviroment {
    public function getProperty(string $name): string;
}