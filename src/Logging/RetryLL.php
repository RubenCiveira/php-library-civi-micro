<?php
namespace Civi\Micro\Logging;

use Civi\Micro\Resilience\Retry;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class RetryLL implements MethodInterceptor {
    public function invoke(MethodInvocation $invocation) {
        // Logica de logging
        $method = $invocation->getMethod();

        $attrs = $method->getAttributes(Retry::class);
        $service = '';
        foreach($attrs as $attr) {
            $service = $attr->getArguments()['service'];
        }
        echo "<p>Retry con <b>".$service."</b> before method: ", $method->name, " <br/>";
        return $invocation->proceed();
    }
}