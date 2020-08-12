<?php

return [
    'routes' => [
        'edit' => \App\Controller\EditController::class,
        'display' => \App\Controller\DisplayController::class,
    ],
    'default' => \App\Controller\IndexController::class,
];