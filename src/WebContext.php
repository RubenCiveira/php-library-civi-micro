<?php
namespace Civi\Micro;

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

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
            return $handler->handle( $this->filter( $request ) );
        });
        $routes($app, $this->build());
        $app->run();
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