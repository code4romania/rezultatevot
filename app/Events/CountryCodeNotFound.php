<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Election;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CountryCodeNotFound
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $country;

    public Election $election;

    /**
     * Create a new event instance.
     */
    public function __construct(string $country, Election $election)
    {
        $this->country = $country;
        $this->election = $election;
    }
}
