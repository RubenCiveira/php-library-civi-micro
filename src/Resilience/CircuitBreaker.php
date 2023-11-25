<?php
namespace Civi\Micro\Resilience;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class CircuitBreaker {
    public function __construct(public string $service) {}
}