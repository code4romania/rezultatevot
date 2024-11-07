<?php

declare(strict_types=1);

namespace App\Imports\Siruta;

use App\Models\Locality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocalitiesImport implements ToModel, WithBatchInserts, WithHeadingRow
{
    public function model(array $row): ?Model
    {
        if ($row['niv'] === 1) {
            return null;
        }

        return new Locality([
            'id' => $row['siruta'],
            'level' => $row['niv'],
            'type' => $row['tip'],
            'county_id' => Cache::get('siruta:jud:' . $row['jud']),
            'name' => Str::of($row['denloc'])
                ->replace(['Ţ', 'Ş'], ['Ț', 'Ș'])
                ->title()
                ->remove('București')
                ->trim(),
            'parent_id' => $row['niv'] === 3
                ? $row['sirsup']
                : null,
        ]);
    }

    public function batchSize(): int
    {
        return 4000;
    }
}
