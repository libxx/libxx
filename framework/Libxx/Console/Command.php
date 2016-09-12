<?php

namespace Libxx\Console;

use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;
use \Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends ConsoleCommand implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * Get a service by id.
     *
     * @param string $id
     * @return mixed
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Create a table.
     *
     * @param OutputInterface $output
     * @param array $header
     * @param array $rows
     * @param bool $sep whether to separate rows.
     * @return Table
     */
    protected function createTable(OutputInterface $output, $header, array $rows, $sep = true)
    {
        $table = new Table($output);
        $table->setHeaders($header);
        foreach ($rows as $index => $row) {
            $table->addRow($row);
            if ($sep && $index < count($rows) - 1) {
                $table->addRow(new TableSeparator());
            }
        }
        return $table;
    }

    /**
     * Get value description.
     *
     * @param mixed $value
     * @return array
     */
    protected function getValueDesc($value)
    {
        $type = gettype($value);
        if (is_array($value)) {
            $value = trim(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } elseif (is_object($value)) {
            $value = get_class($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            $length = mb_strlen($type, "utf-8");
            $type .= "(" . $length . ")";
        } elseif (is_numeric($value)) {
        } else {
            $value = 'unsupported display type.';
        }
        return [$type, $value];
    }
}
