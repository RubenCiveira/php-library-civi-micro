<?php
namespace Civi\Micro\Exception;

use RuntimeException;

class NotFoundException extends RuntimeException {
    public function __construct(public readonly string $reference) {}
}