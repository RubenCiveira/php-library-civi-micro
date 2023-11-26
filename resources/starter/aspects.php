<?php
use Ray\Aop\Matcher;
use Ray\Di\AbstractModule;
use Civi\Micro\ImplAop\LoggerAspect;
use Civi\Micro\ImplAop\Resilience\CircuitBreakerAspect;
use Civi\Micro\ImplAop\Resilience\RetryAspect;
use Civi\Micro\Resilience\CircuitBreaker;
use Civi\Micro\Resilience\Retry;

return function(AbstractModule $module, Matcher $matcher) {
    $module->bindInterceptor(
        $matcher->any(),                           // any class
        $matcher->annotatedWith(CircuitBreaker::class),  // #[NotOnWeekends] attributed method
        [CircuitBreakerAspect::class]                          // apply WeekendBlocker interceptor
    );
    $module->bindInterceptor(
        $matcher->any(),                           // any class
        $matcher->annotatedWith(Retry::class),  // #[NotOnWeekends] attributed method
        [RetryAspect::class]                          // apply WeekendBlocker interceptor
    );
    // $module->bindInterceptor(
    //     $matcher->any(),
    //     $matcher->any(),
    //     [LoggerAspect::class]
    // );
};