<?php
namespace Civi\Micro;

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

// INFO: deberíamos mantener controlado el consumo de ram
header("HTTP/1.0 200 OK");
ini_set('memory_limit', '16M');

class WebContext extends Context {
    private static $_filters = [];

    public static function registerFilters($filters) {
        self::$_filters[] = $filters;
    }

    public function __construct(private readonly string $basePath) {
    }

    public function start(\Closure $routes) {
        $app = AppFactory::create();
        $app->add( function (Request $request, RequestHandler $handler) {
            $uri = $request->getUri();
            $path = $uri->getPath();

            if (str_starts_with($path, $this->basePath)) {
                $path = substr($path, strlen($this->basePath));
                $uri = $uri->withPath($path);
                $request = $request->withUri($uri);
            }
            $response = $handler->handle( $this->filter( $request ) );
            // Cambia la versión del protocolo a 1.0
            $protocoloHttp = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'UK');
            return $protocoloHttp === 'HTTP/1.0' ? $response->withProtocolVersion('1.0') : $response;
        });
        $routes($app, $this->build());
        $app->run();
        $size = memory_get_usage();
        $unit=array('b','kb','mb','gb','tb','pb');
        $size = round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
        echo "<p>Consumo: " . $size . "</p>";
    }

    private function filter(Request $request): Request {
        $container = $this->currentContainer();
        foreach(self::$_filters as $filters) {
            foreach($filters as $filter) {
                $filterBean = $container->get( $filter );
                $request = $filterBean->filter($request);
            }
        }
        return $request;
    }
}