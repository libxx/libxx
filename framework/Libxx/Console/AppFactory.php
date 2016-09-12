<?php

namespace Libxx\Console;

use Interop\Container\ContainerInterface;
use Libxx\Container\ContainerAwareInterface;
use Libxx\Kernel\App;
use Libxx\Routing\RouterInterface;
use Symfony\Component\Console\Application;

class AppFactory
{

    /**
     * Factory the console application instance.
     *
     * @param ContainerInterface $container
     * @return Application
     */
    public function __invoke(ContainerInterface $container)
    {
        $container->get(App::class)->boot();

        $config = $container->get('config');
        $app = new Application($config->get('app_name', 'App Console'), $config->get('app_version', 'undefined'));
        $commands = $this->createCommands($container, $config->get('console_commands', []));
        $app->addCommands($commands);
        return $app;
    }

    /**
     * Get the app commands.
     *
     * @param ContainerInterface $container
     * @param array $config
     * @return Command[]
     */
    private function createCommands(ContainerInterface $container, array $config)
    {
        $commands = [];
        foreach ($config as $key => $commandClass) {
            $name = is_int($key) ? null : $key;
            $command = new $commandClass($name);
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
            $commands[] = $command;
        }
        return $commands;
    }
}
