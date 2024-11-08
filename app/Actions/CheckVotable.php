<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Str;

class CheckVotable
{
    private function getIndependentCandidatePrefix(): string
    {
        return config('import.independent_candidate_prefix');
    }

    public function isIndependentCandidate(string $name): bool
    {
        return Str::startsWith($name, $this->getIndependentCandidatePrefix());
    }

    public function getName(string $name): string
    {
        return Str::afterLast($name, $this->getIndependentCandidatePrefix());
    }
}
