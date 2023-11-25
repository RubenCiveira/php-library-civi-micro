<?php
namespace Civi\Micro\Logging;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class Logging implements MethodInterceptor {
    public function invoke(MethodInvocation $invocation) {
        // Logica de logging
        echo "Logging before method: ", $invocation->getMethod()->name, "\n";
        return $invocation->proceed();
    }
}