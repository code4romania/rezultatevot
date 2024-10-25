<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MissingSourceFileException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("Missing source file: {$path}");
    }
}
