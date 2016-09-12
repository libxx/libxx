<?php

namespace App\Console\Command\IDE;

use Libxx\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PHPStormCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('ide:phpstorm')
            ->setDescription('Generate PhpStorm metadata file.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->container;
        if (method_exists($container, 'keys')) {
            $serviceLocators = [
                '\Interop\Container\ContainerInterface::get(\'\')',
                '\Libxx\Console\Command::get(\'\')',
                '\Libxx\Middleware\Controller::get(\'\')',
            ];
            $lines = [
                '<?php',
                'namespace PHPSTORM_META {',
                '    /** @noinspection PhpUnusedLocalVariableInspection */',
                '    /** @noinspection PhpIllegalArrayKeyTypeInspection */',
                '    $STATIC_METHOD_TYPES = [',
            ];
            foreach ($serviceLocators as $serviceLocator) {
                $lines[] = sprintf('        %s => [', $serviceLocator);
                foreach ($container->keys() as $key) {
                    if (interface_exists($key) || class_exists($key)) {
                        $type = $key;
                    } else {
                        $service = $container->get($key);
                        if (is_object($service)) {
                            $type = get_class($service);
                        } else {
                            $type = gettype($service);
                        }
                    }
                    $lines[] = '            ' . sprintf("'%s' instanceof \\%s,", $key, $type);
                }
                $lines[] = '        ],';
            }
            $lines[] = '    ];';
            $lines[] = '}';
            $content = implode("\n", $lines) . PHP_EOL;
            $filename = '.phpstorm.meta.php';
            $appPath = $this->get('config')->get('base_path');
            if (file_put_contents($appPath . '/' . $filename, $content) > 0) {
                $output->writeln("{$filename} generated.");
            } else {
                $output->writeln("generated failed.");
            }
        }
    }
}
