<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class CountryCodeNotFoundException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Could not find country code for: {$name}");
    }
}
