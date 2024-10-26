<?php

declare(strict_types=1);

namespace App\Contracts;

interface TemporaryTable
{
    public function getTemporaryTable(): string;

    public function getTemporaryTableUniqueColumns(): array;

    public static function saveToTemporaryTable(array $values): void;
}
