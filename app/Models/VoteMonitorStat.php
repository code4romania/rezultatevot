<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Enums\VoteMonitorStatKey;
use Database\Factories\VoteMonitorStatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class VoteMonitorStat extends Model
{
    use BelongsToElection;
    /** @use HasFactory<VoteMonitorStatFactory> */
    use HasFactory;

    protected static string $factory = VoteMonitorStatFactory::class;

    protected $fillable = [
        'election_id',
        'key',
        'value',
        'enabled',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'key' => VoteMonitorStatKey::class,
            'value' => 'integer',
            'enabled' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function toArray(): array
    {
        return [
            'value' => Number::format($this->value),
            'key' => $this->key->value,
            'label' => $this->key->getLabel(),
            'icon' => $this->key->getIcon(),
            'color' => $this->key->getColor(),
        ];
    }
}
