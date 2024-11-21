<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VoteMonitorStatKey: string implements HasLabel, HasIcon, HasColor
{
    use Arrayable;
    use Comparable;

    case OBSERVERS = 'observers';
    case COUNTIES = 'counties';
    case POLLING_STATIONS = 'polling_stations';
    case MESSAGES = 'messages';
    case PROBLEMS = 'problems';

    protected function labelKeyPrefix(): ?string
    {
        return 'app.stats';
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OBSERVERS => __('app.vote_monitor_stats.observers'),
            self::COUNTIES => __('app.vote_monitor_stats.counties'),
            self::POLLING_STATIONS => __('app.vote_monitor_stats.polling_stations'),
            self::MESSAGES => __('app.vote_monitor_stats.messages'),
            self::PROBLEMS => __('app.vote_monitor_stats.problems'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OBSERVERS => 'gmdi-remove-red-eye-tt',
            self::COUNTIES => 'gmdi-map-tt',
            self::POLLING_STATIONS => 'gmdi-how-to-vote-tt',
            self::MESSAGES => 'gmdi-send-to-mobile-tt',
            self::PROBLEMS => 'gmdi-report-problem-tt',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OBSERVERS => Color::Purple,
            self::COUNTIES => Color::Purple,
            self::POLLING_STATIONS => Color::Purple,
            self::MESSAGES => Color::Purple,
            self::PROBLEMS => Color::Amber,
        };
    }
}
