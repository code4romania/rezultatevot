<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidSourceUrlException extends Exception
{
    public function __construct(string $url)
    {
        parent::__construct("Invalid source url: {$url}");
    }
}
