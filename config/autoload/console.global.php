<?php

return [
    'console_commands' => [
        \App\Console\Command\IDE\PHPStormCommand::class,
        \App\Console\Command\Debug\ListConfigCommand::class,
        \App\Console\Command\Debug\ListRouteCommand::class,
    ]
];