<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Candidates;

use App\Filament\Admin\Resources\Candidates\Pages\ManageCandidates;
use App\Filament\Imports\SimpleCandidateImporter;
use App\Models\Candidate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 21;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.nomenclature');
    }

    public static function getModelLabel(): string
    {
        return __('app.candidate.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.candidate.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.field.name'))
                    ->required()
                    ->maxLength(255),

                ColorPicker::make('color')
                    ->label(__('app.field.color')),

                Select::make('party_id')
                    ->relationship('party', 'name')
                    ->label(__('app.field.party')),

                SpatieMediaLibraryFileUpload::make('image')
                    ->label(__('app.field.logo'))
                    ->columnSpanFull(),
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
            ->headerActions([
                ImportAction::make()
                    ->importer(SimpleCandidateImporter::class)
                    ->options([
                        'election_id' => Filament::getTenant()->id,
                        'candidate_list' => true,
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

    public static function getPages(): array
    {
        return [
            'index' => ManageCandidates::route('/'),
        ];
    }
}
