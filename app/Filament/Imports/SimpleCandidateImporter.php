<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Actions\CheckVotable;
use App\Models\Candidate;
use App\Models\Party;
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
        static::$model = app(CheckVotable::class)->isIndependentCandidate($this->data['name'])
            ? Candidate::class
            : Party::class;

        return static::getModel()::firstOrNew([
            'name' => $this->data['name'],
            'election_id' => $this->options['election_id'],
        ]);
    }

    protected function afterValidate(): void
    {
        $this->data['name'] = app(CheckVotable::class)->getName($this->data['name']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your candidate import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
