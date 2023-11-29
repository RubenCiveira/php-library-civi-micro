<?php
namespace Civi\Micro\Impl;

use Civi\Micro\Context;
use Civi\Micro\Enviroment;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class EnviromentImpl implements Enviroment {
    static $paths = [];

    private $vars = [];

    public function __construct() {
        $base = Context::getBasePath();
        foreach(self::$paths as $path) {
            $this->vars = array_merge( $this->vars, $this->parse($base . $path) );
        }
    }

    public function has(string $name): bool {
        return isset( $this->vars[$name] );
    }

    public function property(string $name, $default='') {
        return $this->vars[$name] ?? $default;
    }

    private function parse($file) {
        try {
            return $this->plainArray( Yaml::parseFile( $file ) );
        } catch (ParseException $e) {
            printf("No se pudo parsear el archivo YAML: %s", $e->getMessage());
        }
    }

    private function plainArray($array, $prefix=''): array {
        $resultado = [];
        foreach ($array as $clave => $valor) {
            $claveCompleta = $prefix === '' ? $clave : $prefix . '.' . $clave;
            if (is_array($valor) ) {
                $resultado = array_merge($resultado, $this->plainArray($valor, $claveCompleta));
            } else if( !is_numeric($clave) ) {
                $resultado[$claveCompleta] = $valor;
            } else {
                $resultado[$clave] = $value;
            }
        }
        return $resultado;
    }
}

EnviromentImpl::$paths = ['resources/config/application.yaml'];