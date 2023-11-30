<?php
namespace Civi\Micro\Migration;

use PDO;
use PDOException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Civi\Micro\Context;
use Civi\Micro\SpecificConfig;
use Ray\Di\InjectorInterface;

class MigrationService {
    public function __construct(#[SpecificConfig(name: 'main')] private readonly PDO $pdo, private readonly InjectorInterface $injector) {
    }

    public function run() {
        if( $this->lock() ) {
            try {
                $files = $this->retrieveFiles();
                $prevs = $this->listExecuted();
                // Tengo que buscar el primero que necesita ser ejecutado.
                // Si despues de ese fichero pendiente de ejecución sube => salimos
                $error = '';
                $pending = false;
                $to_execute = [];
                foreach($files as $file) {
                    $md5 = md5_file($file);
                    if( isset($prevs[$file]) && !$prevs[$file]['error'] ) {
                        // Hay un script previo pendiente, pero este "posterior" se ejecutó...
                        if( $pending ) {
                            $error .= 'The migration ' . $file . ' was executed, but ' . $pending . ' is waiting';
                        }
                        $prev = $prevs[$file];
                        if( $prev && $md5 != $prev['md5sum']) {
                            $error .= 'The file ' . $file . ' has a differnt md5.';
                        }
                    } else {
                        if( !$pending ) {
                            $pending = $file;
                        }
                        $to_execute[] = $file;
                    }
                }
                if( $error ) {
                    echo 'Hay errores en la migracion ' . $error;
                } else if( $to_execute ) {
                    // TODO: lo suyo sería crear aqui una transacción.
                    foreach($to_execute as $file) {
                        $md5 = md5_file($file);
                        $exists = isset($prevs[$file]);
                        if( str_ends_with($file, '.sql' ) ) {
                            $this->runSql( $file, $exists, $md5 );
                        } else {
                            $this->runPhp( $file, $exists, $md5 );
                        }
                    }
                }
            } finally {
                $this->unlock();
            }
        } else {
            echo 'Esta bloqueado, no puedo ejecutar';
        }
    }

    private function runSql($file, $exists, $md5) {
        $lines = file($file);
        $filtered = array_filter($lines, function($line) {
            return !preg_match('/^--/', $line);
        });
        $sql = implode("\r\n", $filtered);
        // $sql = file_get_contents($file);
        $sentencias = preg_split('/;\s*(\n|$)/', $sql);
        try {
            $this->pdo->beginTransaction();
            foreach($sentencias as $sentencia) {
                if( $sentencia ) {
                    $stmt = $this->pdo->prepare($sentencia);
                    $stmt->execute();
                }
            }
            $this->pdo->commit();
            $this->markOk($file, $exists, $md5);
        } catch(\Exception $ex) {
            $this->pdo->rollBack();
            $this->markFail($file, $exists, $md5, $ex);
        }
    }
    private function runPhp($file, $exists, $md5) {
        try {
            (require $file)($this->injector);
            $this->markOk($file, $exists, $md5);
        } catch(\Exception $ex) {
            $this->markFail($file, $exists, $md5, $ex);
        }
    }
    
    private function markOk($file, $exists, $md5) {
        $stmt = $this->pdo->prepare($exists
            ? 'update $databaselog set filename=:file, md5sum=:sum, error=null, execution=NOW() where filename=:file'
            : 'insert into $databaselog (filename, md5sum, error, execution) values (:file, :sum, NULL, NOW())');
        $stmt->execute(['file' => $file, 'sum' => $md5]);
    }

    private function markFail($file, $exists, $md5, $ex) {
        $stmt = $this->pdo->prepare($exists
            ? 'update $databaselog set filename=:file, md5sum=:sum, error=:error, execution=NOW() where filename=:file'
            : 'insert into $databaselog (filename, md5sum, error, execution) values (:file, :sum, :error, NOW())');
        $stmt->execute(['file' => $file, 'sum' => $md5, 'error' => substr( $ex->getMessage(), 0, 150) ]);
        echo "Error falta con " . $ex->getMessage();
    }

    private function listExecuted() {
        $this->createLogTable();
        $stmt = $this->pdo->prepare('select filename, md5sum, error from $databaselog order by execution asc');
        $rows = [];
        $stmt->execute();
        while($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[$fila['filename']] = $fila;
        }
        return $rows;
    }

    private function lock(): bool {
        $this->createLockTable();
        $stmt = $this->pdo->prepare('select locked from $databaselock where id=1');
        $stmt->execute();
        $locked = false;
        if ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $locked = $fila['locked'];
        }
        if( $locked ) {
            return false;
        } else {
            $update = $this->pdo->prepare('update $databaselock set locked=1, granted=NOW() where id=1');
            $update->execute();
            return true;
        }
    }

    private function retrieveFiles() {
        $base = Context::getBasePath();
        $directorioRaiz = $base . 'resources/migrations';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directorioRaiz),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $archivos[] = $fileinfo->getPathname();
            }
        }
        sort($archivos, SORT_STRING);
        return $archivos;
    }

    private function unlock() {
        $update = $this->pdo->prepare('update $databaselock set locked=0, granted=NULL where id=1');
        $update->execute();
    }

    private function createLockTable() {
        try {
            $stmt = $this->pdo->prepare('create table $databaselock (id int(11), locked bit(1), granted datetime)');
            $stmt->execute();
            $stmt = $this->pdo->prepare('insert into $databaselock values (1, 0, NULL)');
            $stmt->execute();
        } catch(PDOException $ex) {
            $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if( 'mysql' != $driver || $ex->getCode() != '42S01' ) {
                echo "Error de PDO: " . $ex->getMessage();
            }
        }
    }
    private function createLogTable() {
        try {
            $stmt = $this->pdo->prepare('create table $databaselog (filename varchar(250), md5sum varchar(35), execution datetime, error varchar(250) )');
            $stmt->execute();
        } catch(PDOException $ex) {
            $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if( 'mysql' != $driver || $ex->getCode() != '42S01' ) {
                echo "Error de PDO: " . $ex->getMessage();
            }
        }
    }
}