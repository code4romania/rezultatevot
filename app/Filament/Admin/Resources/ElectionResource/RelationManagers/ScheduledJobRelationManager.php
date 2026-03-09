<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ElectionResource\RelationManagers;

use App\Enums\Cron;
use App\Jobs\SchedulableJob;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use HaydenPierce\ClassFinder\ClassFinder;

class ScheduledJobRelationManager extends RelationManager
{
    protected static string $relationship = 'scheduledJobs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('job')
                    ->label(__('app.field.job'))
                    ->options(function () {
                        ClassFinder::disablePSR4Vendors();

                        $classes = ClassFinder::getClassesInNamespace('App\Jobs', ClassFinder::RECURSIVE_MODE);

                        return collect($classes)
                            ->filter(fn (string $class) => is_subclass_of($class, SchedulableJob::class))
                            ->mapWithKeys(fn (string $job) => [
                                $job => $job::name(),
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('cron')
                    ->label(__('app.field.cron'))
                    ->options(Cron::options())
                    ->enum(Cron::class)
                    ->required(),

                Fieldset::make('source')
                    ->label('Source')
                    ->columns(2)
                    ->schema([
                        TextInput::make('source_url')
                            ->label(__('app.field.source_url'))
                            ->columnSpanFull(),

                        TextInput::make('source_username')
                            ->label(__('app.field.source_username')),

                        TextInput::make('source_password')
                            ->label(__('app.field.source_password'))
                            ->password(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('job')
            ->columns([
                ToggleColumn::make('is_enabled')
                    ->label(__('app.field.is_enabled'))
                    ->shrink(),

                TextColumn::make('job')
                    ->label(__('app.field.job'))
                    ->description(fn (string $state) => $state, 'above')
                    ->formatStateUsing(fn (string $state) => rescue(fn () => $state::name())),

                TextColumn::make('cron')
                    ->label(__('app.field.cron')),

                TextColumn::make('last_run_at')
                    ->label(__('app.field.last_run_at'))
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
