<?php

declare(strict_types=1);

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LocationColumn extends TextColumn
{
    protected function setUp(): void
    {
        $this->label(__('app.field.location'));

        $this->searchable(
            query: fn (Builder $query, string $search) => $query
                ->whereRelation('country', 'countries.name', 'like', "%{$search}%")
                ->orWhereRelation('county', 'counties.name', 'like', "%{$search}%")
                ->orWhereRelation('locality', 'localities.name', 'like', "%{$search}%")
        );

        $this->state(function (Model $record) {
            if ($record->country) {
                return $record->country->name;
            }

            return \sprintf('%s, %s', $record->locality->name, $record->county->name);
        });

        $this->description(function (Model $record) {
            if ($record->country) {
                return $record->country->id;
            }

            return $record->locality->id;
        });

        $this->icon(function (Model $record) {
            if (data_get($record, 'has_issues', false)) {
                return 'heroicon-s-exclamation-triangle';
            }

            return null;
        });

        $this->iconColor(function (Model $record) {
            if (data_get($record, 'has_issues', false)) {
                return 'warning';
            }

            return null;
        });
    }
}
