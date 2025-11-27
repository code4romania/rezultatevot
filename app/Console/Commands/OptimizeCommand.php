<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Foundation\Console\OptimizeCommand as BaseCommand;

/**
 * Workaround for `Unable to locate a class or view for component` error.
 *
 * @link https://github.com/laravel/framework/issues/50619#issuecomment-2902099714
 */
class OptimizeCommand extends BaseCommand
{
    protected function getOptimizeTasks(): array
    {
        $commands = parent::getOptimizeTasks();
        $commands = ['views' => $commands['views']] + $commands;

        return $commands;
    }
}
