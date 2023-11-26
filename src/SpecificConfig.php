<?php
namespace Civi\Micro;

use Attribute;
use Ray\Di\Di\Qualifier;
use Ray\Di\InjectionPointInterface;
use Civi\Micro\Enviroment;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class SpecificConfig
{
    public static function env(InjectionPointInterface $ip, Enviroment $env): Enviroment {
        $pprs = $ip->getParameter()->getAttributes( SpecificConfig::class );
        $preffix = '';
        foreach($pprs as $ppr) {
            $args = $ppr->getArguments();
            if( isset($args['name']) ) {
                $preffix = $args['name'] . '.';
            }
        }
        return new ContextEnv($preffix, $env);
    }
    public function __construct(public readonly string $name) {
    }
}

class ContextEnv implements Enviroment {
    public function __construct(private readonly string $preffix, private readonly Enviroment $env) {
    }

    public function has(string $name): bool {
        return $this->env->has('env.' . $this->preffix . $name) || $this->env->has('env.' . $name);
    }

    public function property(string $name): string {
        $value = $this->env->property('env.' . $this->preffix . $name);
        return $this->env->has('env.' . $this->preffix . $name) ? $this->env->property('env.' . $this->preffix . $name) :  $this->env->property('env.' . $name);
    }
}