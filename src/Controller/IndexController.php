<?php

namespace App\Controller;

use App\Service\ViewService;

final class IndexController implements ControllerInterface
{
    public function run(): void
    {
        (new ViewService())->display('index', ['a' => 'b']);
    }
}