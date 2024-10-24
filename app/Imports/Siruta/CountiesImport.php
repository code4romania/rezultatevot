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
            'code' => $this->getCountyCode($row['siruta']),
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

    protected function getCountyCode(int $siruta): string
    {
        return [
            10 => 'AB',
            29 => 'AR',
            38 => 'AG',
            47 => 'BC',
            56 => 'BH',
            65 => 'BN',
            74 => 'BT',
            83 => 'BV',
            92 => 'BR',
            109 => 'BZ',
            118 => 'CS',
            127 => 'CJ',
            136 => 'CT',
            145 => 'CV',
            154 => 'DB',
            163 => 'DJ',
            172 => 'GL',
            181 => 'GJ',
            190 => 'HR',
            207 => 'HD',
            216 => 'IL',
            225 => 'IS',
            234 => 'IF',
            243 => 'MM',
            252 => 'MH',
            261 => 'MS',
            270 => 'NT',
            289 => 'OT',
            298 => 'PH',
            305 => 'SM',
            314 => 'SJ',
            323 => 'SB',
            332 => 'SV',
            341 => 'TR',
            350 => 'TM',
            369 => 'TL',
            378 => 'VS',
            387 => 'VL',
            396 => 'VN',
            403 => 'B',
            519 => 'CL',
            528 => 'GR',
        ][$siruta];
    }
}
