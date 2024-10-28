<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\DB;

trait HasTemporaryTable
{
    public function getTemporaryTable(): string
    {
        return '_temp_' . $this->getTable();
    }

    /**
     * @param array<string|int> $values
     */
    public static function saveToTemporaryTable(array $values): void
    {
        DB::table(app(static::class)->getTemporaryTable())->insert($values);
    }
}
