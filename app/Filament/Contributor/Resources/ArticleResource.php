<?php

declare(strict_types=1);

namespace App\Filament\Contributor\Resources;

use App\Filament\Contributor\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Election;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.newsfeed');
    }

    public static function getModelLabel(): string
    {
        return __('app.article.singular');
    }

    public static function getPluralLabel(): string
    {
        return __('app.article.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->label(__('app.article.title'))
                            ->maxLength(255),

                        Select::make('election_id')
                            ->required()
                            ->relationship('election', 'slug')
                            ->options(
                                fn () => Election::query()
                                    ->where('is_live', true)
                                    ->whereHas('contributors', fn ($query) => $query->where('id', auth()->id()))
                                    ->pluck('slug', 'id')
                            )
                            ->label(__('app.article.election')),

                        RichEditor::make('content')
                            ->required()
                            ->label(__('app.article.content'))
                            ->columnSpanFull(),
                    ]),

                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->multiple()
                            ->reorderable()
                            ->previewable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('author_id', auth()->id()))
            ->columns([
                TextColumn::make('id')
                    ->prefix('#')
                    ->sortable()
                    ->shrink(),

                TextColumn::make('title')
                    ->searchable()
                    ->label(__('app.article.title'))
                    ->sortable(),

                TextColumn::make('election.slug')
                    ->sortable()
                    ->label(__('app.article.election'))
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->formatStateUsing(fn (?Carbon $state) => $state?->toDateTimeString())
                    ->sortable()
                    ->label(__('app.article.published_at'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('election')
                    ->relationship('election', 'name')
                    ->label(__('app.article.election'))
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
