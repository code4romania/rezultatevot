<?php

declare(strict_types=1);

namespace App\Enums;

enum Time: int
{
    case MINUTE_IN_SECONDS = 60;
    case HOUR_IN_SECONDS = 60 * 60;
    case DAY_IN_SECONDS = 60 * 60 * 24;
    case WEEK_IN_SECONDS = 60 * 60 * 24 * 7;
    case MONTH_IN_SECONDS = 60 * 60 * 24 * 7 * 30;
    case YEAR_IN_SECONDS = 60 * 60 * 24 * 7 * 30 * 365;
}
