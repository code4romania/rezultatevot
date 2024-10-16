<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ElectionTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionType extends Model
{
    /** @use HasFactory<ElectionTypeFactory> */
    use HasFactory;

    protected static string $factory = ElectionTypeFactory::class;

    protected $fillable = [
        'name',
    ];

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class, 'type_id');
    }
}
