<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Cron: string implements HasLabel
{
    use Arrayable;
    use Comparable;

    case EVERY_MINUTE = '* * * * *';
    case EVERY_2_MINUTES = '*/2 * * * *';
    case EVERY_3_MINUTES = '*/3 * * * *';
    case EVERY_4_MINUTES = '*/4 * * * *';
    case EVERY_5_MINUTES = '*/5 * * * *';
    case EVERY_10_MINUTES = '*/10 * * * *';
    case EVERY_5_1_MINUTES = '1-59/5 * * * *';
    case EVERY_5_2_MINUTES = '2-59/5 * * * *';
    case EVERY_5_3_MINUTES = '3-59/5 * * * *';
    case EVERY_5_4_MINUTES = '4-59/5 * * * *';
    case EVERY_10_5_MINUTES = '5-59/10 * * * *';
    case EVERY_10_6_MINUTES = '6-59/10 * * * *';
    case EVERY_10_7_MINUTES = '7-59/10 * * * *';
    case EVERY_10_8_MINUTES = '8-59/10 * * * *';
    case EVERY_10_9_MINUTES = '9-59/10 * * * *';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EVERY_MINUTE => __('app.cron.every_minute'),
            self::EVERY_2_MINUTES => __('app.cron.every_2_minutes'),
            self::EVERY_3_MINUTES => __('app.cron.every_3_minutes'),
            self::EVERY_4_MINUTES => __('app.cron.every_4_minutes'),
            self::EVERY_5_MINUTES => __('app.cron.every_5_minutes'),
            self::EVERY_10_MINUTES => __('app.cron.every_10_minutes'),
            self::EVERY_5_1_MINUTES => __('app.cron.every_5_1_minutes'),
            self::EVERY_5_2_MINUTES => __('app.cron.every_5_2_minutes'),
            self::EVERY_5_3_MINUTES => __('app.cron.every_5_3_minutes'),
            self::EVERY_5_4_MINUTES => __('app.cron.every_5_4_minutes'),
            self::EVERY_10_5_MINUTES => __('app.cron.every_10_5_minutes'),
            self::EVERY_10_6_MINUTES => __('app.cron.every_10_6_minutes'),
            self::EVERY_10_7_MINUTES => __('app.cron.every_10_7_minutes'),
            self::EVERY_10_8_MINUTES => __('app.cron.every_10_8_minutes'),
            self::EVERY_10_9_MINUTES => __('app.cron.every_10_9_minutes'),
        };
    }
}
