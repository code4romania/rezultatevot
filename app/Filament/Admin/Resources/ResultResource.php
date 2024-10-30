<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultResource\Pages;
use App\Models\Result;
use App\Tables\Columns\LocationColumn;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('app.result.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.result.label.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('country', 'county', 'locality'))
            ->columns([
                LocationColumn::make('location'),

                TextColumn::make('eligible_voters_total')
                    ->label(__('app.field.eligible_voters_total'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('eligible_voters_permanent')
                    ->label(__('app.field.eligible_voters_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('eligible_voters_special')
                    ->label(__('app.field.eligible_voters_special'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('present_voters_total')
                    ->label(__('app.field.present_voters_total'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('present_voters_permanent')
                    ->label(__('app.field.present_voters_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('present_voters_special')
                    ->label(__('app.field.present_voters_special'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('present_voters_supliment')
                    ->label(__('app.field.present_voters_supliment'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('papers_received')
                    ->label(__('app.field.papers_received'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('papers_unused')
                    ->label(__('app.field.papers_unused'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('votes_valid')
                    ->label(__('app.field.votes_valid'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('votes_null')
                    ->label(__('app.field.votes_null'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->paginated([10, 25, 50, 100])
            ->deferLoading()
            ->recordClasses(fn (Result $result) => $result->has_issues ? 'bg-warning-50' : null);
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
            'index' => Pages\ManageResults::route('/'),
        ];
    }
}
