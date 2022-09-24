<?php

declare(strict_types=1);

namespace App\Model;

class Statistic
{
    /** @var string[] */
    public array $success = [];

    /** @var string[] */
    public array $failed = [];

    /** @var string[] */
    public array $offline = [];

    /** @var string[] */
    public array $online = [];

    /** @var string[] */
    public array $notified = [];

    /** @var string[] */
    public array $pushed = [];
}
