<?php
namespace Civi\Micro\Filter;

use Civi\Micro\RequestFilter;
use Psr\Http\Message\ServerRequestInterface as Request;

class OAuthFilter implements RequestFilter {
    public function filter(Request $request): Request {
        // Tiene que evaluar cabeceras y toda la pesca
        echo "<h4>Filtro de oauth para saber si tiene usuario</h4>";
        return $request;
    }
}

// Esta clase se ejecutará al inicio de la aplicación