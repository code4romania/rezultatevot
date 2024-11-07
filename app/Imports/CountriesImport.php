<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CountriesImport implements ToModel, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row): ?Model
    {
        $row = collect($row)
            ->filter();

        return new Country([
            'id' => $row->shift(),
            'name' => Str::trim($row->shift()),
            'aliases' => $row,
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }
}
