<?php

declare(strict_types=1);

namespace App\Actions;

class CheckRecordHasIssues
{
    public function checkTurnout(array $record): bool
    {
        $computedTotal = collect(['LP', 'LC', 'LS', 'UM'])
            ->map(fn (string $key) => $record[$key])
            ->sum();

        if ($computedTotal !== $record['LT']) {
            return true;
        }

        return false;
    }

    public function checkRecord(array $record): bool
    {
        if ($record['a'] != $record['a1'] + $record['a2']) {
            return true;
        }

        if ($record['a1'] < $record['b1']) {
            return true;
        }

        if ($record['a2'] < $record['b2']) {
            return true;
        }

        if ($record['b'] != $record['b1'] + $record['b2'] + $record['b3']) {
            return true;
        }

        if ($record['c'] < $record['d'] + $record['e'] + $record['f']) {
            return true;
        }

        return false;
    }
}
