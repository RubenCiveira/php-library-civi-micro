<?php
namespace Civi\Micro\Exception;

use RuntimeException;

class OptimistLockException extends RuntimeException {
    public function __construct(public readonly string $ref, public readonly string $lock) {}
}