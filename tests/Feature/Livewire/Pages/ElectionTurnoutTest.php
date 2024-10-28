<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\ElectionTurnout;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ElectionTurnoutTest extends TestCase
{
    #[Test]
    public function renders_successfully()
    {
        Livewire::test(ElectionTurnout::class)
            ->assertStatus(200);
    }
}
