<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\TemporaryTable;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class PersistTemporaryTableData implements ShouldQueue
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

        $columns = collect($model->getFillable())
            ->sort();

        $selectColumns = $columns
            ->map(fn (string $column) => "`$column`")
            ->implode(', ');

        $updateColumns = $columns
            ->reject(fn (string $column) => \in_array($column, $model->getTemporaryTableUniqueColumns()))
            ->map(fn (string $column) => "`$column` = `{$model->getTemporaryTable()}`.`$column`")
            ->implode(', ');

        DB::unprepared(<<<"SQL"
            INSERT INTO `{$model->getTable()}` ({$selectColumns})
            SELECT {$selectColumns} FROM `{$model->getTemporaryTable()}` WHERE `election_id` = {$this->electionId}
            ON DUPLICATE KEY UPDATE {$updateColumns};
        SQL);
    }
}
