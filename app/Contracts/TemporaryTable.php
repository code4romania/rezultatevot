<?php

declare(strict_types=1);

namespace App\Contracts;

interface TemporaryTable
{
    public function getTemporaryTable(): string;

    /**
     * @return array<string>
     */
    public function getTemporaryTableUniqueColumns(): array;

    /**
     * @param array<string|int> $values
     */
    public static function saveToTemporaryTable(array $values): void;
}
