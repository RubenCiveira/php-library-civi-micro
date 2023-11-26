<?php
namespace Civi\Micro\Impl;

use Civi\Micro\Enviroment;

class EnviromentImpl implements Enviroment {
    public function has(string $name): bool {
        return $this->property($name) !== '';
    }
    public function property(string $name): string {
        $host = '127.0.0.1'; // Host de la base de datos
        $dbname = 'civi-micro'; // Nombre de la base de datos
        $username = 'root'; // Usuario de la base de datos
        $password = 'toor'; // Contraseña del usuario
        if( 'env.url' == $name ) {
            return "mysql:host=$host;dbname=$dbname;charset=utf8";
        }
        if( 'env.username' == $name ) {
            return $username;
        }
        if( 'env.password' == $name ) {
            return $password;
        }
        return '';
    }
}