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
    case EVERY_5_1_MINUTES = '1-59/5 * * * *';
    case EVERY_5_2_MINUTES = '2-59/5 * * * *';
    case EVERY_5_3_MINUTES = '3-59/5 * * * *';
    case EVERY_5_4_MINUTES = '4-59/5 * * * *';
    case EVERY_10_MINUTES = '*/10 * * * *';
    case EVERY_10_1_MINUTES = '1-59/10 * * * *';
    case EVERY_10_2_MINUTES = '2-59/10 * * * *';
    case EVERY_10_3_MINUTES = '3-59/10 * * * *';
    case EVERY_10_4_MINUTES = '4-59/10 * * * *';
    case EVERY_10_5_MINUTES = '5-59/10 * * * *';
    case EVERY_10_6_MINUTES = '6-59/10 * * * *';
    case EVERY_10_7_MINUTES = '7-59/10 * * * *';
    case EVERY_10_8_MINUTES = '8-59/10 * * * *';
    case EVERY_10_9_MINUTES = '9-59/10 * * * *';
    case EVERY_20_MINUTES = '*/20 * * * *';
    case EVERY_20_1_MINUTES = '1-59/20 * * * *';
    case EVERY_20_2_MINUTES = '2-59/20 * * * *';
    case EVERY_20_3_MINUTES = '3-59/20 * * * *';
    case EVERY_20_4_MINUTES = '4-59/20 * * * *';
    case EVERY_20_5_MINUTES = '5-59/20 * * * *';
    case EVERY_20_6_MINUTES = '6-59/20 * * * *';
    case EVERY_20_7_MINUTES = '7-59/20 * * * *';
    case EVERY_20_8_MINUTES = '8-59/20 * * * *';
    case EVERY_20_9_MINUTES = '9-59/20 * * * *';
    case EVERY_30_MINUTES = '*/30 * * * *';
    case EVERY_30_1_MINUTES = '1-59/30 * * * *';
    case EVERY_30_2_MINUTES = '2-59/30 * * * *';
    case EVERY_30_3_MINUTES = '3-59/30 * * * *';
    case EVERY_30_4_MINUTES = '4-59/30 * * * *';
    case EVERY_30_5_MINUTES = '5-59/30 * * * *';
    case EVERY_30_6_MINUTES = '6-59/30 * * * *';
    case EVERY_30_7_MINUTES = '7-59/30 * * * *';
    case EVERY_30_8_MINUTES = '8-59/30 * * * *';
    case EVERY_30_9_MINUTES = '9-59/30 * * * *';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EVERY_MINUTE => __('app.cron.every_minute'),
            self::EVERY_2_MINUTES => __('app.cron.every_2_minutes'),
            self::EVERY_3_MINUTES => __('app.cron.every_3_minutes'),
            self::EVERY_4_MINUTES => __('app.cron.every_4_minutes'),
            self::EVERY_5_MINUTES => __('app.cron.every_5_minutes'),
            self::EVERY_5_1_MINUTES => __('app.cron.every_5_1_minutes'),
            self::EVERY_5_2_MINUTES => __('app.cron.every_5_2_minutes'),
            self::EVERY_5_3_MINUTES => __('app.cron.every_5_3_minutes'),
            self::EVERY_5_4_MINUTES => __('app.cron.every_5_4_minutes'),
            self::EVERY_10_MINUTES => __('app.cron.every_10_minutes'),
            self::EVERY_10_1_MINUTES => __('app.cron.every_10_1_minutes'),
            self::EVERY_10_2_MINUTES => __('app.cron.every_10_2_minutes'),
            self::EVERY_10_3_MINUTES => __('app.cron.every_10_3_minutes'),
            self::EVERY_10_4_MINUTES => __('app.cron.every_10_4_minutes'),
            self::EVERY_10_5_MINUTES => __('app.cron.every_10_5_minutes'),
            self::EVERY_10_6_MINUTES => __('app.cron.every_10_6_minutes'),
            self::EVERY_10_7_MINUTES => __('app.cron.every_10_7_minutes'),
            self::EVERY_10_8_MINUTES => __('app.cron.every_10_8_minutes'),
            self::EVERY_10_9_MINUTES => __('app.cron.every_10_9_minutes'),
            self::EVERY_20_MINUTES => __('app.cron.every_20_minutes'),
            self::EVERY_20_1_MINUTES => __('app.cron.every_20_1_minutes'),
            self::EVERY_20_2_MINUTES => __('app.cron.every_20_2_minutes'),
            self::EVERY_20_3_MINUTES => __('app.cron.every_20_3_minutes'),
            self::EVERY_20_4_MINUTES => __('app.cron.every_20_4_minutes'),
            self::EVERY_20_5_MINUTES => __('app.cron.every_20_5_minutes'),
            self::EVERY_20_6_MINUTES => __('app.cron.every_20_6_minutes'),
            self::EVERY_20_7_MINUTES => __('app.cron.every_20_7_minutes'),
            self::EVERY_20_8_MINUTES => __('app.cron.every_20_8_minutes'),
            self::EVERY_20_9_MINUTES => __('app.cron.every_20_9_minutes'),
            self::EVERY_30_MINUTES => __('app.cron.every_30_minutes'),
            self::EVERY_30_1_MINUTES => __('app.cron.every_30_1_minutes'),
            self::EVERY_30_2_MINUTES => __('app.cron.every_30_2_minutes'),
            self::EVERY_30_3_MINUTES => __('app.cron.every_30_3_minutes'),
            self::EVERY_30_4_MINUTES => __('app.cron.every_30_4_minutes'),
            self::EVERY_30_5_MINUTES => __('app.cron.every_30_5_minutes'),
            self::EVERY_30_6_MINUTES => __('app.cron.every_30_6_minutes'),
            self::EVERY_30_7_MINUTES => __('app.cron.every_30_7_minutes'),
            self::EVERY_30_8_MINUTES => __('app.cron.every_30_8_minutes'),
            self::EVERY_30_9_MINUTES => __('app.cron.every_30_9_minutes'),
        };
    }
}
