<?php
namespace Civi\Micro;

use Psr\Http\Message\ServerRequestInterface as Request;

class WebContext extends Context {
    private static $_filters = [];

    public static function registerFilters($filters) {
        self::$_filters[] = $filters;
    }

    public function filter(Request $request): Request {
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