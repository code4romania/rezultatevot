<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\ElectionTurnouts;
use App\Models\Election;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ElectionTurnoutsTest extends TestCase
{
    #[Test]
    public function renders_successfully()
    {
        $election = Election::factory()
            ->create();

        Livewire::test(ElectionTurnouts::class)
            ->assertOk();
    }
}
