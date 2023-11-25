<?php
namespace Civi\Micro;

use Psr\Http\Message\ServerRequestInterface as Request;

interface RequestFilter {
    public function filter(Request $request): Request;
}