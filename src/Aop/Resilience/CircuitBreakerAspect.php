<?php
namespace Civi\Micro\Aop\Resilience;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Civi\Micro\Resilience\CircuitBreaker;

class CircuitBreakerAspect implements MethodInterceptor {
    public function invoke(MethodInvocation $invocation) {
        // Logica de logging
        // Logica de logging
        $method = $invocation->getMethod();

        $attrs = $method->getAttributes(CircuitBreaker::class);
        $service = '';
        foreach($attrs as $attr) {
            $service = $attr->getArguments()['service'];
        }
        echo "<p>CircuitBreaker con <b>".$service."</b> before method: ", $method->name, " <br/>";
        return $invocation->proceed();
    }
}