<?php

declare(strict_types=1);

namespace App\Contracts;

interface ClearsCache
{
    public function clearCache(int $electionId): bool;
}
