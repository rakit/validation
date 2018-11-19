<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rule;

class Required extends Rule
{

    public function check($value): bool
    {
        return true;
    }
}
