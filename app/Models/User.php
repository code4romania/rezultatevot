<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName, HasMedia, HasTenants
{
    use HasApiTokens;
    use HasRole;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

    protected static string $factory = UserFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function elections(): BelongsToMany
    {
        return $this->belongsToMany(Election::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl(
                \sprintf(
                    'https://ui-avatars.com/api/?%s',
                    Arr::query([
                        'name' => Str::initials($this->name),
                        'color' => 'FFFFFF',
                        'background' => '09090B',
                    ])
                )
            )
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Contain, 64, 64)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('large')
                    ->fit(Fit::Contain, 256, 256)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        if ($panel->getId() === 'contributor') {
            return $this->isContributor();
        }

        return false;
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($panel->getId() === 'admin') {
            return Election::all();
        }

        return $this->elections;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($tenant instanceof Election) {
            return $this->elections->contains($tenant);
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }
}
