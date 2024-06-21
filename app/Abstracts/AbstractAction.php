<?php

declare(strict_types=1);

namespace App\Abstracts;

use Illuminate\Foundation\Bus\Dispatchable;

abstract class AbstractAction
{
    use Dispatchable;
    abstract public function handle();
}
