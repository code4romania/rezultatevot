<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\VoteMonitorStatKey;
use App\Filament\Admin\Resources\VoteMonitorStatResource\Pages;
use App\Models\VoteMonitorStat;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class VoteMonitorStatResource extends Resource
{
    protected static ?string $model = VoteMonitorStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('key')
                    ->options(VoteMonitorStatKey::options())
                    ->enum(VoteMonitorStatKey::class)
                    ->unique('vote_monitor_stats', 'key', null, true, function($rule){
                        return $rule->where('election_id', filament()->getTenant()->id);
                    })
                    ->required(),

                TextInput::make('value')
                    ->type('number')
                    ->minValue(0)
                    ->maxValue(4294967295)
                    ->required(),

                Checkbox::make('enabled')
                    ->label('Enabled')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->alignRight()
                    ->shrink(),

                ToggleColumn::make('enabled')
                    ->label('Enabled')
                    ->shrink(),

                TextColumn::make('key')
                    ->label('Name')
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVoteMonitorStats::route('/'),
        ];
    }
}
