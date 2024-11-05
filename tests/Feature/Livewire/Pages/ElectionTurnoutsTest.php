<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\ElectionTurnouts;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ElectionTurnoutsTest extends TestCase
{
    #[Test]
    public function renders_successfully()
    {
        Livewire::test(ElectionTurnouts::class)
            ->assertStatus(200);
    }
}
