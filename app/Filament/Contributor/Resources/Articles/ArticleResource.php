<?php

declare(strict_types=1);

namespace App\Filament\Contributor\Resources\Articles;

use App\Filament\Contributor\Resources\Articles\Pages\CreateArticle;
use App\Filament\Contributor\Resources\Articles\Pages\EditArticle;
use App\Filament\Contributor\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use App\Models\Election;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('app.article.singular');
    }

    public static function getPluralLabel(): string
    {
        return __('app.article.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('app.article.title'))
                            ->maxLength(255)
                            ->required(),

                        Select::make('election_id')
                            ->label(__('app.article.election'))
                            ->relationship(
                                'election',
                                'slug',
                                fn ($query) => $query
                                    ->where('is_live', true)
                                    ->whereHas('contributors', fn ($query) => $query->where('id', auth()->id()))
                            )
                            ->required(),

                        RichEditor::make('content')
                            ->label(__('app.article.content'))
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->multiple()
                            ->reorderable()
                            ->previewable(false),
                    ]),

                Section::make()
                    ->schema([
                        Repeater::make('embeds')
                            ->label(__('app.article.embeds'))
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('html')
                                    ->label(__('app.article.html'))
                                    ->required(),
                            ]),
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
                    ->relationship('election', 'slug')
                    ->label(__('app.article.election'))
                    ->options(
                        fn () => Election::query()
                            ->where('is_live', true)
                            ->whereHas('contributors', fn ($query) => $query->where('id', auth()->id()))
                            ->pluck('slug', 'id')
                    )
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
