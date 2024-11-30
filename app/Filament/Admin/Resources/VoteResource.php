<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VoteResource\Pages;
use App\Filament\Filters\LocationFilter;
use App\Models\Vote;
use App\Tables\Columns\LocationColumn;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.election_data');
    }

    public static function getModelLabel(): string
    {
        return __('app.vote.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.vote.label.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('country', 'county', 'locality'))
            ->columns([
                LocationColumn::make('location'),

                TextColumn::make('section')
                    ->label(__('app.field.section'))
                    ->searchable(),

                TextColumn::make('part'),

                TextColumn::make('votable.name')
                    ->label(__('app.field.candidate'))
                    ->searchable(),

                TextColumn::make('votes')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                LocationFilter::make(),
            ])
            ->paginated([10, 25, 50, 100])
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVotes::route('/'),

        ];
    }
}
