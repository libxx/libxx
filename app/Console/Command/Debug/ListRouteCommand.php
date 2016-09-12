<?php

namespace App\Console\Command\Debug;

use Libxx\Console\Command;
use Libxx\Routing\RouterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListRouteCommand extends Command
{


    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('debug:list-route')
            ->setDescription('List registered routes.');
    }
    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->container->get(RouterInterface::class);
        $routes = $router->getRoutes();
        if ($routes) {
            $rows = [];
            foreach ($routes as $key => $route) {
                $rows[] = [$route->getName(), implode(',', $route->getMethods()), $route->getPath(), $this->getValueText($route->getMiddleware()), $this->getValueText($route->getContext())];
            }
            $table = $this->createTable($output, ['name', 'method', 'path', 'middleware', 'context'], $rows);
            $table->render();
        } else {
            $output->writeln("No route registered.");
        }
    }
    protected function getValueText($value)
    {
        list(, $value) = $this->getValueDesc($value);
        return $value;
    }
}
