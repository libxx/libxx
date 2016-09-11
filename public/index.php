<?php

use Libxx\Kernel\App;

define('APP_STARTED_AT', microtime(true));

chdir('../');

$container = require('bootstrap/container.php');
/* @var $container \Interop\Container\ContainerInterface */

$app = $container->get(App::class);
/* @var $app App */

$app->run();