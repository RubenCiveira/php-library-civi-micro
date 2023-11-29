<?php
namespace Civi\Micro\Exception;

class ConstraintException extends \RuntimeException {
    public function __construct(public readonly string $reason, public readonly array $values) {}
}