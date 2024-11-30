<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Enums\Cron;
use App\Exceptions\InvalidSourceUrlException;
use App\Exceptions\MissingSourceUrlException;
use Database\Factories\ScheduledJobFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScheduledJob extends Model
{
    use BelongsToElection;
    /** @use HasFactory<ScheduledJobFactory> */
    use HasFactory;

    protected static string $factory = ScheduledJobFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job',
        'cron',
        'is_enabled',
        'source_url',
        'source_username',
        'source_password',
        'last_run_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'source_username',
        'source_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cron' => Cron::class,
            'is_enabled' => 'boolean',
            'source_username' => 'encrypted',
            'source_password' => 'encrypted',
            'last_run_at' => 'datetime',
        ];
    }

    public function requiresAuthentication(): bool
    {
        return filled($this->source_username) && filled($this->source_password);
    }

    /**
     * @param  array<string, string>     $map
     * @return string
     * @throws MissingSourceUrlException
     * @throws InvalidSourceUrlException
     */
    public function getPreparedSourceUrl(array $map = []): string
    {
        if (blank($this->source_url)) {
            throw new MissingSourceUrlException;
        }

        $search = array_keys($map);
        $replace = array_values($map);

        $url = filter_var(
            Str::replace($search, $replace, $this->source_url, caseSensitive: false),
            \FILTER_SANITIZE_URL
        );

        if (filter_var($url, \FILTER_VALIDATE_URL) === false) {
            throw new InvalidSourceUrlException($url);
        }

        return $url;
    }

    public function fetchSource(array $map = []): Response
    {
        return Http::createPendingRequest()
            ->when($this->requiresAuthentication(), function (PendingRequest $request) {
                return $request->withBasicAuth($this->source_username, $this->source_password);
            })
            ->get($this->getPreparedSourceUrl($map))
            ->throw();
    }

    public function disk(): Filesystem
    {
        return Storage::disk(config('import.disk'));
    }

    public function getSourcePath(string $filename): string
    {
        return \sprintf('source/%s/%s/%s', $this->election_id, $this->id, $filename);
    }
}
