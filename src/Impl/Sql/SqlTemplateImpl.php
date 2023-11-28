<?php
namespace Civi\Micro\Impl\Sql;

use \PDO;
use \Clousure;
use Civi\Micro\Sql\SqlTemplate;

class SqlTemplateImpl implements SqlTemplate {

    public function __construct(private readonly PDO $pdo) {}

    public function execute($query, array $params): bool {
        $stmt = $this->pdo->prepare(query);
        return $stmt->execute($params);
    }

    public function query($query, array $params, Clousure $clousure): array {
        $stmt = $this->pdo->prepare(query);
        $stmt->query( $params );
        $keys = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys[] = $clousure->call( $file );
        }
        return $keys;
    }

    public function findOne($query, array $param, Clousure $clousure) {
        $stmt = $this->pdo->prepare(query);
        $stmt->query( $params );
        $key = null;
        return $fila = $stmt->fetch(PDO::FETCH_ASSOC) ? $clousure->call( $file ) : null;
    }

    public function exists($query, array $param): bool {
        $stmt = $this->pdo->prepare(query);
        $stmt->query( $params );
        $key = null;
        return $stmt->fetch();
    }
}