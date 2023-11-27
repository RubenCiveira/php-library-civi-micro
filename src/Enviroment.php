<?php
namespace Civi\Micro;

interface Enviroment {
    public function has(string $name): bool;
    public function property(string $name, $default='');
}