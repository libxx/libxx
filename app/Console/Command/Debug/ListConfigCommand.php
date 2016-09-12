<?php

namespace App\Console\Command\Debug;

use Libxx\Console\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListConfigCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('debug:list-config')
            ->setDescription('List application config.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->container->get('config');
        $settings = $config->all();
        if ($settings) {
            $rows = [];
            foreach ($settings as $key => $value) {
                list($type, $value) = $this->getValueDesc($value);
                $rows[] = [new TableCell($key), new TableCell($type), new TableCell($value)];
            }
            $table = $this->createTable($output, ['key', 'type', 'value'], $rows);
            $table->render();
        } else {
            $output->writeln("No config.");
        }
    }
}
