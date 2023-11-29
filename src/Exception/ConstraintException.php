<?php
namespace Civi\Micro\Exception;

use RuntimeException;

class ConstraintException extends RuntimeException {
    public function __construct(public readonly string $reason, public readonly array $values) {}
}