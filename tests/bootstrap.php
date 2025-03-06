<?php

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
}

// executes the "php bin/console cache:clear" command

unlink(sprintf('%s', dirname(__DIR__) . '/var/test.db'));;
passthru('php bin/console --env=test doctrine:database:create');
passthru('php bin/console --env=test doctrine:schema:create');
