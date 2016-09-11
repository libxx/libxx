<?php

$cachedConfigFile = 'runtime/cache/config.php';

$config = [];

if (is_file($cachedConfigFile)) {
    $config = require($cachedConfigFile);
} else {
    $files = glob(__DIR__ . '/autoload/{*.global.php,*.local.php}', GLOB_BRACE);

    foreach ($files as $file) {
        $config = \Libxx\Helper\Arr::merge($config, require($file));
    }

    if (isset($config['config_cache_enabled']) && $config['config_cache_enabled']) {
        file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ';');
    }
}

return $config;