<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MissingSourceUrlException extends Exception
{
    public function __construct()
    {
        parent::__construct('Missing source url');
    }
}
