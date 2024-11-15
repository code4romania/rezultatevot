<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Tabs extends Component
{
    public Election $election;

    public Collection $items;

    public string $page;

    public function __construct(Election $election, string $page, array $parameters = [])
    {
        $this->election = $election;

        $this->page = $page;

        $this->items = collect([
            [
                'current' => $page === 'turnout',
                'label' => __('app.navigation.turnout'),
                'url' => route('front.elections.turnout', [
                    'election' => $election,
                    ...$parameters,
                ]),
            ],
            [
                'current' => $page === 'results',
                'label' => __('app.navigation.results'),
                'url' => route('front.elections.results', [
                    'election' => $election,
                    ...$parameters,
                ]),
            ],
            ...collect($election->properties?->get('tabs'))
                ->map(fn ($url, $label) => [
                    'label' => $label,
                    'url' => $url,
                ])
                ->all(),
        ]);
    }

    public function render(): View
    {
        return view('components.election.tabs');
    }

    public function isCurrent(array $item): bool
    {
        return data_get($item, 'current', false);
    }
}
