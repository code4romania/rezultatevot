<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CandidateResource\Pages;
use App\Models\Candidate;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('app.field.name'))
                    ->required()
                    ->maxLength(255),

                ColorPicker::make('color')
                    ->label(__('app.field.color')),

                Select::make('party_id')
                    ->relationship('party', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->conversion('thumb')
                    ->shrink(),

                ColorColumn::make('color')
                    ->label(__('app.field.color'))
                    ->shrink(),

                TextColumn::make('name')
                    ->label(__('app.field.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('party.name')
                    ->label(__('app.field.party'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCandidates::route('/'),
        ];
    }
}
