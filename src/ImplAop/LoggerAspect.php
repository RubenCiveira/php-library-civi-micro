<?php
namespace Civi\Micro\ImplAop;

use Civi\Micro\Resilience\Retry;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class LoggerAspect implements MethodInterceptor {
    public function invoke(MethodInvocation $invocation) {
        // Logica de logging
        $method = $invocation->getMethod();
        echo "<p>Log execution of method: ", $method->name, " <br/>";
        return $invocation->proceed();
    }
}