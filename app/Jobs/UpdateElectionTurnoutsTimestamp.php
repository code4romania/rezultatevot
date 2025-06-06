<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Election;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class UpdateElectionTurnoutsTimestamp implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public Election $election;

    /**
     * Create a new job instance.
     */
    public function __construct(int $electionId)
    {
        $this->election = Election::find($electionId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->election->touch('turnouts_updated_at');
    }
}
