<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VoteResource\Pages;

use App\Filament\Admin\Resources\VoteResource;
use App\Jobs\Mandates\GenerateChamberDeputiesMandatesJob;
use App\Jobs\Mandates\GenerateSenateMandatesJob;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;

class ManageVotes extends ManageRecords
{
    protected static string $resource = VoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mandates')
                ->label(__('app.mandate.action.generate'))
                ->form([
                    Select::make('type')
                        ->options([
                            's' => 'Senat',
                            'cd' => 'Camera Deputaților',
                        ])
                        ->required(),

                ])
                ->action(function (array $data) {
                    $election = Filament::getTenant();

                    match (data_get($data, 'type')) {
                        's' => GenerateSenateMandatesJob::dispatch($election),
                        'cd' => GenerateChamberDeputiesMandatesJob::dispatch($election),
                    };
                }),

        ];
    }
}
