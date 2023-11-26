<?php
namespace Civi\Micro\Impl\OAuth;

use PDO;
use Ramsey\Uuid\Uuid;
use Civi\Micro\OAuth\Persistence;
use Civi\Micro\SpecificConfig;
use Ray\Di\Di\Named;

class PersistenceSql implements Persistence {

    public function __construct(#[SpecificConfig(name: 'main')] private readonly PDO $pdo) {

    }

    private function connect(): PDO {
        $host = '127.0.0.1'; // Host de la base de datos
        $dbname = 'civi-micro'; // Nombre de la base de datos
        $username = 'root'; // Usuario de la base de datos
        $password = 'toor'; // Contraseña del usuario
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        // Configurar el modo de error PDO a excepción
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function getPrivateKey() {
        $pdo = $this->connect();

        $consultaPreparada = "SELECT private_key FROM claves_api where active = true limit 1";
        $stmt = $pdo->prepare($consultaPreparada);
        $stmt->execute();
        $privateKeyContent = '';
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $privateKeyContent = $fila['private_key'];
        }
        return $privateKeyContent;
    }

    public function saveKey($publicKey, $privateKey) {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("UPDATE claves_api set active = false");
        $stmt->execute();

        // $stmt = $pdo->prepare("DELETE claves_api WHERE creation <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        // $stmt->execute();

        $consultaPreparada = "INSERT INTO claves_api(id, public_key, private_key, active, creation) VALUES (:uid, :public, :private, true, NOW())";
        $stmt = $pdo->prepare($consultaPreparada);
        $stmt->execute(['uid' => Uuid::uuid4(), 'public' => $publicKey, 'private' => $privateKey]);
    }

    public function listKeys() {
        $pdo = $this->connect();
        $consultaPreparada = "SELECT public_key FROM claves_api limit 10";
        $stmt = $pdo->prepare($consultaPreparada);
        $stmt->execute();
        $keys = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys[] =$fila['public_key'];
        }
        return $keys;
    }
}