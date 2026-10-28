<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Parties;

use App\Filament\Admin\Resources\Parties\Pages\ManageParties;
use App\Filament\Imports\SimpleCandidateImporter;
use App\Models\Party;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartyResource extends Resource
{
    protected static ?string $model = Party::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.nomenclature');
    }

    public static function getModelLabel(): string
    {
        return __('app.party.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.party.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.field.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('acronym')
                    ->label(__('app.field.acronym'))
                    ->required()
                    ->maxLength(255),

                ColorPicker::make('color')
                    ->label(__('app.field.color'))
                    ->required(),

                SpatieMediaLibraryFileUpload::make('logo')
                    ->label(__('app.field.logo'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->conversion('thumb')
                    ->shrink(),

                ColorColumn::make('color')
                    ->label(__('app.field.color'))
                    ->shrink(),

                TextColumn::make('name')
                    ->label(__('app.field.name'))
                    ->description(fn (Party $record) => $record->acronym)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(SimpleCandidateImporter::class)
                    ->options([
                        'election_id' => Filament::getTenant()->id,
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ManageParties::route('/'),
        ];
    }
}
