<?php

declare(strict_types=1);

namespace App\Jobs;

class DummyJob extends SchedulableJob
{
    public static function name(): string
    {
        return 'Dummy Job';
    }

    public function execute(): void
    {
        logger()->info('Dummy job executed', [
            'job' => $this->scheduledJob,
        ]);
    }
}
