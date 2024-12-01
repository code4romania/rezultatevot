<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Enums\DataLevel;
use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SeatsChart extends Component
{
    public int $totalSeats;

    public Election $election;

    public DataLevel $level;

    public float $r;

    public float $innerRadius;

    public float $spacing = 2.3;

    public Collection $dots;

    public Collection $votables;

    public function __construct(Election $election, DataLevel $level, Collection $votables)
    {
        $this->election = $election;
        $this->level = $level;
        $this->totalSeats = (int) $election->properties?->get('total_seats');

        $this->r = 45.345892868 / sqrt($this->totalSeats);

        $this->votables = $votables;
        $this->dots = $this->generateDots();
    }

    public function shouldRender(): bool
    {
        return $this->level->is(DataLevel::TOTAL)
            && $this->election->has_lists
            && $this->totalSeats > 0;
    }

    public function render(): View
    {
        return view('components.seats-chart');
    }

    protected function generateDots(): Collection
    {
        // Determine how many dots fit in each row
        $R = 100 - $this->r;
        $seatsLeft = $this->totalSeats;
        $rows = [];

        while ($seatsLeft > 0) {
            // Offset the first and last dot by a tiny angle so that they're tangent to the bottom of the viewport
            $offset = asin($this->r / $R);
            $seatsOnRow = 1 + max(0, floor(((pi() - $offset * 2) * $R) / ($this->r * $this->spacing)));

            $seatsLeft -= $seatsOnRow;

            $rows[] = [
                'R' => $R,
                'offset' => $offset,
                'seatsOnRow' => $seatsOnRow,
            ];

            $R -= $this->r * $this->spacing;
        }

        $rowsCount = \count($rows);

        // We might have some empty slots left over that we need to
        // remove evenly from amongst the rows.
        if ($seatsLeft < 0) {
            $div = floor(-$seatsLeft / $rowsCount);

            for ($i = 0; $i < $rowsCount; $i++) {
                $rows[$i]['seatsOnRow'] -= $div;
            }
            // $rows = $rows->map(function (array $row) use ($div) {
            //     $row['seatsOnRow'] -= $div;

            //     return $row;
            // });

            $seatsLeft += $div * $rowsCount;
        }

        // If they don't split evenly, remove the rest starting with the innermost row.
        if ($seatsLeft < 0) {
            for ($i = $rowsCount - 1; $i >= 0 && $seatsLeft < 0; $i--) {
                $rows[$i]['seatsOnRow']--;

                $seatsLeft++;
            }

            // $rows = $rows->reverse()->map(function (array $row) use (&$seatsLeft) {
            //     if ($seatsLeft < 0) {
            //         $row['seatsOnRow']--;

            //         $seatsLeft++;
            //     }

            //     return $row;
            // });
        }

        $dots = collect();

        // Position each dot in polar coordinates
        for ($i = 0; $i < $rowsCount; $i++) {
            $seatsOnRow = $rows[$i]['seatsOnRow'];
            $offset = $rows[$i]['offset'];
            $R = $rows[$i]['R'];

            $stride = (pi() - $offset * 2) / ($seatsOnRow - 1);

            for ($j = 0; $j < $seatsOnRow; $j++) {
                $alpha = $offset + $j * $stride;

                $dots->push([
                    'cx' => 100 - $R * cos($alpha),
                    'cy' => 100 - $R * sin($alpha),
                    // 'alpha' => $offset + $stride * $j,
                    // 'R' => $R,
                    'candidate' => null,
                ]);
            }
        }

        // Sort the dots radially left to right
        $dots = $dots->sortBy('alpha');

        // Assign candidates to each dot
        $start = 0;
        $end = $dots->count() - 1;
        $fromStart = false; // Alternate each candidate left/right

        $this->votables->each(function ($votable, int $index) use (&$dots, &$start, &$end, &$fromStart) {
            $seats = $votable->seats;

            if (blank($seats) || $seats <= 0) {
                return;
            }

            $fromStart = ! $fromStart;

            while ($seats > 0 && $start <= $end) {
                $dots[$fromStart ? $start : $end]['candidate'] = $index;

                if ($fromStart) {
                    $start++;
                } else {
                    $end--;
                }
            }
        });

        $this->innerRadius = $rows[$rowsCount - 1]['R'] - $this->r;

        return $dots;
    }
}
