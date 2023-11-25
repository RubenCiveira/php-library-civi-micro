<?php
use Civi\Micro\Context;
use Civi\Micro\WebContext;

Context::registerAspects(require dirname(__FILE__).'/config/aspects.php');
Context::registerDefinitions(require dirname(__FILE__).'/config/definitions.php');
WebContext::registerFilters(require dirname(__FILE__).'/config/filters.php');