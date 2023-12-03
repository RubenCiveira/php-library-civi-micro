<?php
namespace Civi\Micro\Impl\Sql;

use PDO;
use PDOException;
use Closure;
use Civi\Micro\Sql\SqlTemplate;
use Civi\Micro\Sql\SqlParam;
use Civi\Micro\Sql\NotUniqueException;

class SqlTemplateImpl implements SqlTemplate {

    public function __construct(private readonly PDO $pdo) {}

    public function begin() {
        $this->pdo->beginTransaction();
    }

    public function rollback() {
        $this->pdo->rollBack();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function execute($query, array $params): bool {
        try {
            $stmt = $this->prepare($query, $params);
            $result = $stmt->execute();
            return $result ? $stmt->rowCount() : 0;
        } catch(PDOException $ex) {
            $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $code = 'pgsql' == $driver ? 23505 : 23000;
            if( $ex->getCode() == $code ) {
                throw new NotUniqueException($ex);
            }
            throw $ex;
        }
    }

    public function query($query, array $params, Closure $clousure): array {
        $stmt = $this->prepare($query, $params);
        $stmt->execute();
        $keys = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys[] = $clousure($fila );
        }
        return $keys;
    }

    public function findOne($query, array $params, Closure $clousure) {
        $stmt = $this->prepare($query, $params);
        $stmt->execute();
        $key = null;
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $clousure($fila) : null;
    }

    public function exists($query, array $params): bool {
        $stmt = $this->prepare($query, $params);
        $stmt->execute();
        $key = null;
        return !!$stmt->fetch();
    }

    private function prepare($query, $params) {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if( 'mysql' == $driver ) {
            $query = str_replace('"', "`", $query);
        }
        $theParams = [];
        foreach($params as $key => $param) {
            $value = is_a($param, SqlParam::class) ? $param->value : $param;
            $name = is_a($param, SqlParam::class) ? $param->name : $key;
            if( is_array($value) ) {
                $pattern = '/\s+(IN|in)\s*\(\s*:' . preg_quote($name, '/') . '\s*\)/i';
                $query = preg_replace($pattern, $this->paramExpand($name, $value), $query);
            }
        }
        $stmt = $this->pdo->prepare($query);
        foreach($params as $key => $param) {
            $value = is_a($param, SqlParam::class) ? $param->value : $param;
            $name = is_a($param, SqlParam::class) ? $param->name : $key;
            if( is_array($value) ) {
                for($i=0; $i<count($value); $i++) {
                    if( is_a($param, SqlParam::class)) {
                        $stmt->bindValue($name.'_'.($i+1), $value[$i], $this->podType( $param->type) );
                    } else {
                        $stmt->bindValue($name.'_'.($i+1), $value[$i]);
                    }    
                }
            } else {
                if( is_a($param, SqlParam::class)) {
                    $stmt->bindValue($name, $value, $this->podType( $param->type) );
                } else {
                    $stmt->bindValue($name, $value);
                }
            }
        }
        return $stmt;// ->execute();
    }

    private function paramExpand($name, $elements) {
        $params = [];
        for ($i = 1; $i <= count($elements); $i++) {
            $params[] = ':'.$name.'_'.$i;
        }
        return ' in (' . implode(', ', $params) . ')';
    }

    private function podType($type) {
        if( $type == SqlParam::BOOL ) {
            return PDO::PARAM_BOOL;
        } else if( $type == SqlParam::INT ) {
            return PDO::PARAM_INT;
        } else if( $type == SqlParam::TEXT ) {
            return PDO::PARAM_LOB;
        } else {
            return PDO::PARAM_STR;
        }
    }
}