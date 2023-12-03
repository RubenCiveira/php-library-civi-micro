<?php
namespace Civi\Micro;

use Ray\Di\AbstractModule;
use Ray\Di\Injector;
use Ray\Di\InjectorInterface;
use Ray\Compiler\ScriptInjector;
use Ray\Compiler\DiCompiler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Koriym\Attributes\AttributeReader;
use Ray\ServiceLocator\ServiceLocator;

ServiceLocator::setReader(new AttributeReader());

class Context extends AbstractModule {
    private static $_aspect_list = [];
    private static $_definitions_list = [];
    private static $_base_path = '';
    
    public static function configBasePath($path) {
        self::$_base_path = $path;
    }

    public static function getBasePath(): string {
        return self::$_base_path;
    }

    public static function registerDefinitions($definitions) {
        self::$_definitions_list[] = $definitions;
    }
    
    public static function registerAspects($aspects) {
        self::$_aspect_list[] = $aspects;
    }
    
    private ContextContainer $container;
    private string $_cache;

    public function cache(string $cache) {
        // FIXME: controlar el root.
        $this->_cache = $cache;
    }
    
    public function build(): ContextContainer {
        if( $this->_cache && is_writable($this->_cache) ) {
            if( !file_exists($this->_cache.'/_compile.log') ) {
                if( count( scandir($this->_cache) ) > 2 ) {
                    var_dump( scandir($this->_cache));
                    die('El directorio cache debe estar vacio para usarlo como cache: '.realpath($this->_cache).' no lo está: vacielo a mano.');
                }
                $injector = new DiCompiler($this, $this->_cache);
                $this->configure();
                $injector->compile();
            }
            try {
                $injector = new ScriptInjector($this->_cache);
            } catch (NotCompiled $e) {
                $injector = new DiCompiler($this, $this->_cache);
                $this->configure();
                $injector->compile();
            }
        } else {
            $injector = new Injector($this);
            $this->configure();
        }
        $this->container = new ContextContainer($injector);
        return $this->container;
    }

    public function bind(string $interface = ''): \Ray\Di\Bind {
        return parent::bind($interface);
    }

    protected function currentContainer(): ContextContainer {
        return $this->container;
    }

    #[\Override]
    protected function configure() {
        echo "<h1>DEFS</h1>";
        foreach(self::$_definitions_list as $path) {
            $path($this);
        }
        foreach(self::$_aspect_list as $aspect) {
            $aspect($this, $this->matcher);
        }
    }
}

class ContextContainer implements ContainerInterface {
    public function __construct(private readonly Injector|InjectorInterface $injector) {

    }
    public function get(string $id) {
        return $this->injector->getInstance( $id );
    }

    public function has(string $id): bool {
        return !! $this->injector->getInstance( $id );
    }
}