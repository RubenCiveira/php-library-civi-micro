<?php
namespace Civi\Micro\Impl\Sql;

use PDO;
use PDOException;
use Closure;
use Civi\Micro\Sql\SqlTemplate;
use Civi\Micro\Sql\NotUniqueException;

class SqlTemplateImpl implements SqlTemplate {

    public function __construct(private readonly PDO $pdo) {}

    public function execute($query, array $params): bool {
        $stmt = $this->pdo->prepare(query);
        try {
            return $stmt->execute($params);
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
        $stmt->execute( $params );
        $keys = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys[] = $clousure($fila );
        }
        return $keys;
    }

    public function findOne($query, array $param, Closure $clousure) {
        $stmt = $this->pdo->prepare(query);
        $stmt->query( $params );
        $key = null;
        return $fila = $stmt->fetch(PDO::FETCH_ASSOC) ? $clousure($fila ) : null;
    }

    public function exists($query, array $param): bool {
        $stmt = $this->pdo->prepare(query);
        $stmt->query( $params );
        $key = null;
        return $stmt->fetch();
    }
}