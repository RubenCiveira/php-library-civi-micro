<?php
namespace Civi\Micro\Sql;

class SqlParam {
    public const BOOL = 1;
    public const STR = 2;
    public const INT = 3;
    public const DECIMAL = 4;
    public const TEXT = 5;

    public function __construct(public readonly string $name, public readonly mixed $value, public readonly int $type) {
    }
}
