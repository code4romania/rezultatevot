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

    public static function saveToTemporaryTable(array $values): void
    {
        DB::table((new static())->getTemporaryTable())->insert($values);
    }
}
