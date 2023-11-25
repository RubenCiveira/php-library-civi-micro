<?php
namespace Civi\Micro\Resilience;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class Retry {
    public function __construct(public string $service) {}
}