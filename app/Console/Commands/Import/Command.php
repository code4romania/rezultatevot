<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use Illuminate\Console\Command as BaseCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class Command extends BaseCommand
{
    use ConfirmableTrait;

    protected readonly Connection $db;

    protected ?ProgressBar $progressBar = null;

    protected int $errorsCount = 0;

    public function __construct()
    {
        parent::__construct();

        $this->db = DB::connection('import');
    }

    public function createProgressBar(string $message, int $max): void
    {
        $this->progressBar = $this->output->createProgressBar($max);
        $this->progressBar->setFormat("\n<options=bold>%message%</>\n[%bar%] %current%/%max%\n");
        $this->progressBar->setMessage('⏳ ' . $message);
        $this->progressBar->setMessage('', 'status');
        $this->progressBar->setBarWidth(48);
        $this->progressBar->setBarCharacter('<comment>=</>');
        $this->progressBar->setEmptyBarCharacter('<fg=gray>-</>');
        $this->progressBar->setProgressCharacter('<comment>></>');
        $this->progressBar->start();
    }

    public function finishProgressBar(string $message): void
    {
        if ($this->hasErrors()) {
            $this->progressBar->setMessage('🚨 <fg=red>' . $message . ' with ' . $this->errorsCount . ' errors</>');
        } else {
            $this->progressBar->setMessage('✅ <info>' . $message . '</>');
        }

        $this->progressBar->finish();
        $this->resetErrors();
    }

    public function logError(string $message, array $context = []): void
    {
        logger()->error($message, $context);

        $this->errorsCount++;
    }

    public function hasErrors(): bool
    {
        return $this->errorsCount > 0;
    }

    public function resetErrors(): void
    {
        $this->errorsCount = 0;
    }
}
