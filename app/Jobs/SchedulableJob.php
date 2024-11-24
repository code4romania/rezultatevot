<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ScheduledJob;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class SchedulableJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ScheduledJob $scheduledJob;

    /**
     * Create a new job instance.
     */
    public function __construct(ScheduledJob $scheduledJob)
    {
        $this->scheduledJob = $scheduledJob;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 45;

    abstract public function execute(): void;

    abstract public static function name(): string;

    /**
     * Execute the job.
     */
    final public function handle(): void
    {
        $this->execute();

        $this->scheduledJob->touch('last_run_at');
    }

    public function uniqueId(): string
    {
        return "scheduled-job:{$this->scheduledJob->id}";
    }
}
