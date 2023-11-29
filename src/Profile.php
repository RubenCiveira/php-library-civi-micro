<?php
namespace Civi\Micro;

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use Civi\Micro\Migration\MigrationService;

class Profile {
    public static function run(WebContext $context) {
        $instance = new Profile();
        $context->start(fn($app, $injector) => $instance->routes($app, $injector));

    }

    public function routes(App $app, ContainerInterface $injector) {
        $app->get('/migrations', function (Request $request, Response $response, $args) use($injector) {
            $mig = $injector->get(MigrationService::class);
            $mig->run();
            $response->getBody()->write( "Done" );
            return $response;
        });
    }
}