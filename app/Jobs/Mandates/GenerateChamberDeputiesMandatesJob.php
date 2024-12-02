<?php

declare(strict_types=1);

namespace App\Jobs\Mandates;

use App\Models\Election;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class GenerateChamberDeputiesMandatesJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public Election $election;

    /**
     * Create a new job instance.
     */
    public function __construct(Election $election)
    {
        $this->election = $election;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
