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

    public function execute($query, array $params): bool {
        $stmt = $this->pdo->prepare($query);
        try {
            $result = $this->executeParams($stmt, $params);
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
        $stmt = $this->pdo->prepare($query);
        $this->executeParams($stmt, $params);
        $keys = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys[] = $clousure($fila );
        }
        return $keys;
    }

    public function findOne($query, array $params, Closure $clousure) {
        $stmt = $this->pdo->prepare($query);
        $this->executeParams($stmt, $params);
        $key = null;
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $clousure($fila) : null;
    }

    public function exists($query, array $params): bool {
        $stmt = $this->pdo->prepare($query);
        $this->executeParams($stmt, $params);
        $key = null;
        return !!$stmt->fetch();
    }

    private function executeParams($stmt, $params) {
        foreach($params as $key => $param) {
            if( is_a($param, SqlParam::class)) {
                $value = $param->value;
                $stmt->bindValue($param->name, $value, $this->podType( $param->type) );
            } else {
                $stmt->bindValue($key, $param);
            }
        }
        return $stmt->execute();
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