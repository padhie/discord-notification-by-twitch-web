<?php

namespace App\Service;

use stdClass;

final class TemplateVariables extends stdClass
{
    public function __construct(array $variables) {
        foreach ($variables as $key => $value) {
            $this->{$key} = $value;
        }
    }
}