<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CountriesImport implements ToModel, WithBatchInserts, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row): ?Model
    {
        return new Country([
            'id' => $row['id'],
            'name' => Str::trim($row['name']),
            'aliases' => Str::of($row['aliases'])
                ->explode('|')
                ->filter(),
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }
}
