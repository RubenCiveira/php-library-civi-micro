<?php
namespace Civi\Micro;

use Attribute;
use Ray\Di\Di\Qualifier;
use Ray\Di\InjectionPointInterface;
use Civi\Micro\Enviroment;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class SpecificConfig
{
    public static function env(string $group, InjectionPointInterface $ip, Enviroment $env): Enviroment {
        $pprs = $ip->getParameter()->getAttributes( SpecificConfig::class );
        $preffix = '';
        foreach($pprs as $ppr) {
            $args = $ppr->getArguments();
            if( isset($args['name']) ) {
                $preffix = $args['name'] . '.';
            }
        }
        return new ContextEnv($group, $preffix, $env);
    }
    public function __construct(public readonly string $name) {
    }
}

class ContextEnv implements Enviroment {
    private readonly string $group;
    public function __construct(string $group, private readonly string $preffix, private readonly Enviroment $env) {
        $this->group = 'env.' . ($group ? $group . '.' : '');
    }

    public function has(string $name): bool {
        return $this->env->has($this->group . $this->preffix . $name) || $this->env->has($this->group  . $name);
    }

    public function property(string $name, $default='') {
        return $this->env->has($this->group . $this->preffix . $name) ? 
            $this->env->property($this->group  . $this->preffix . $name) :  
            $this->env->property($this->group . $name, $default);
    }
}