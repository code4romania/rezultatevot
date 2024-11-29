<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Candidate;
use App\Models\Party;
use App\Services\RecordService;
use Carbon\CarbonInterface;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SimpleCandidateImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('acronym')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('color')
                ->rules(['nullable', 'string', 'hex_color']),
        ];
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return null;
    }

    public function resolveRecord(): Candidate|Party
    {
        static::$model = Party::class;
        if (RecordService::isIndependentCandidate($this->data['name']) || $this->options['candidate_list']) {
            static::$model = Candidate::class;
        }

        return static::getModel()::firstOrNew([
            'name' => $this->data['name'],
            'election_id' => $this->options['election_id'],
        ]);
    }

    protected function afterValidate(): void
    {
        $this->data['name'] = RecordService::getName($this->data['name']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = \sprintf(
            'Your candidate import has completed and %d %s imported.',
            number_format($import->successful_rows),
            str('row')->plural($import->successful_rows)
        );

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= \sprintf(
                ' %d %s failed to import.',
                number_format($failedRowsCount),
                str('row')->plural($failedRowsCount)
            );
        }

        return $body;
    }
}
