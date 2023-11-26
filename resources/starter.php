<?php
use Civi\Micro\Context;
use Civi\Micro\WebContext;

Context::registerAspects(require dirname(__FILE__).'/starter/aspects.php');
Context::registerDefinitions(require dirname(__FILE__).'/starter/definitions.php');
WebContext::registerFilters(require dirname(__FILE__).'/starter/filters.php');