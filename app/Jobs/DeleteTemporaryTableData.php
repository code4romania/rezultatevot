<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\TemporaryTable;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class DeleteTemporaryTableData implements ShouldQueue
{
    use Queueable;

    public string $model;

    public int $electionId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $model, int $electionId)
    {
        $this->model = $model;
        $this->electionId = $electionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $model = new $this->model;

        if (! $model instanceof TemporaryTable) {
            throw new Exception('Model must implement TemporaryTable contract');
        }

        DB::table($model->getTemporaryTable())
            ->where('election_id', $this->electionId)
            ->delete();
    }
}
