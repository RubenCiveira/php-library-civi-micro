<?php
use Civi\Micro\Context;

Context::registerAspects(require dirname(__FILE__).'/config/aspects.php');
Context::registerDefinitions(require dirname(__FILE__).'/config/definitions.php');