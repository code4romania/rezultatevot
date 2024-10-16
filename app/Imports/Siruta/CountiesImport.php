<?php

declare(strict_types=1);

namespace App\Imports\Siruta;

use App\Models\County;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CountiesImport implements ToModel, WithBatchInserts, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row): ?Model
    {
        if ($row['niv'] !== 1) {
            return null;
        }

        Cache::put('siruta:jud:' . $row['jud'], $row['siruta'], 1000);

        return new County([
            'id' => $row['siruta'],
            'name' => Str::of($row['denloc'])
                ->replace(['Ţ', 'Ş'], ['Ț', 'Ș'])
                ->title()
                ->remove(['Județul', 'Municipiul'])
                ->trim(),

        ]);
    }

    public function batchSize(): int
    {
        return 50;
    }
}
