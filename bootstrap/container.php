<?php

use Libxx\Container\Container;
use Libxx\Container\ContainerAwareInterface;
use Libxx\Helper\ParameterBag;

require __DIR__ . '/../vendor/autoload.php';

$config = require(__DIR__ . '/../config/config.php');

$container = new Container();

if (isset($config['factories'])) {
    foreach ($config['factories'] as $service => $factory) {
        $container[$service] = function($c) use ($factory) {
            if (is_string($factory)) {
                $factory = new $factory;
            }
            $obj = $factory($c);
            if ($obj instanceof ContainerAwareInterface) {
                $obj->setContainer($c);
            }
            return $obj;
        };
    }

    unset($config['factories']);
}

if (isset($config['objects'])) {
    foreach ($config['objects'] as $service => $object) {
        if (is_int($service)) {
            $service = $object;
        }
        $container[$service] = function($c) use ($object) {
            if (is_string($object)) {
                $object = new $object();
            }
            if ($object instanceof ContainerAwareInterface) {
                $object->setContainer($c);
            }
            return $object;
        };
    }

    unset($config['objects']);
}

$container['config'] = new ParameterBag($config);

return $container;