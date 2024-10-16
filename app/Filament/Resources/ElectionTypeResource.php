<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ElectionTypeResource\Pages;
use App\Models\ElectionType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectionTypeResource extends Resource
{
    protected static ?string $model = ElectionType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 22;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.admin');
    }

    public static function getModelLabel(): string
    {
        return __('app.election_type.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.election_type.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('admin.field.name'))
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.field.name')),

                TextColumn::make('elections_count')
                    ->counts('elections')
                    ->shrink(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),

                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageElectionTypes::route('/'),
        ];
    }
}
