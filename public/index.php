<?php

use App\Service\RouterService;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

try {
    $router = new RouterService();
    $router->callController('');
} catch (Throwable $throwable) {
    echo $throwable->getMessage();
}
