<?php
use Ray\Aop\Matcher;
use Ray\Di\AbstractModule;
use Civi\Micro\Logging\Logging;
use Civi\Micro\Logging\RetryLL;
use Civi\Micro\Resilience\CircuitBreaker;
use Civi\Micro\Resilience\Retry;

return function(AbstractModule $module, Matcher $matcher) {
    $module->bindInterceptor(
        $matcher->any(),                           // any class
        $matcher->annotatedWith(CircuitBreaker::class),  // #[NotOnWeekends] attributed method
        [Logging::class]                          // apply WeekendBlocker interceptor
    );
    $module->bindInterceptor(
        $matcher->any(),                           // any class
        $matcher->annotatedWith(Retry::class),  // #[NotOnWeekends] attributed method
        [RetryLL::class]                          // apply WeekendBlocker interceptor
    );
};