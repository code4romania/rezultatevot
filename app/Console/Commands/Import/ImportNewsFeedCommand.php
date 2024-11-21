<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Enums\User\Role;
use App\Models\Article;
use App\Models\Election;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use stdClass;

class ImportNewsFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:news-feed
        {--chunk=1000 : The number of records to process at a time}
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import news items from the old database.';

    private ?Collection $userIds = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return static::FAILURE;
        }

        Schema::withoutForeignKeyConstraints(function () {
            Article::truncate();
            User::where('role', Role::CONTRIBUTOR)->delete();
        });

        $this->importAuthors();
        $this->importArticles();

        return static::SUCCESS;
    }

    private function importAuthors(): void
    {
        $query = $this->db
            ->table('authors')
            ->orderBy('authors.Id');

        $this->createProgressBar(
            'Importing authors...',
            $query->count()
        );

        $this->userIds = collect();

        $query->each(function (stdClass $row) {
            $user = User::create([
                'name' => $row->Name,
                'role' => Role::CONTRIBUTOR,
                'email' => "contributor-{$row->Id}@example.com",
                'password' => Hash::make(Str::random(32)),
            ]);

            if (filled($row->Avatar)) {
                $user->addMediaFromUrl($row->Avatar)
                    ->toMediaCollection('avatar');
            }

            $this->userIds->put($row->Id, $user->id);
            $this->progressBar->advance();
        });

        $this->finishProgressBar('Imported authors.');
    }

    private function importArticles(): void
    {
        $query = $this->db
            ->table('articles')
            ->orderBy('articles.BallotId');

        $this->createProgressBar(
            'Importing articles...',
            $query->count()
        );

        $elections = Election::pluck('id', 'old_id');

        $media = $this->db
            ->table('articlepictures')
            ->get()
            ->keyBy('ArticleId');

        $query->get()
            ->each(function (stdClass $row) use ($elections, $media) {
                $article = Article::create([
                    'title' => $row->Title,
                    'author_id' => $this->userIds->get($row->AuthorId),
                    'election_id' => $elections->get($row->BallotId),
                    'content' => Str::markdown($row->Body),
                ]);

                if (filled($pics = $media->get($row->Id))) {
                    try {
                        $article->addMediaFromUrl(Str::replace(' ', '%20', $pics->Url))
                            ->toMediaCollection();
                    } catch (\Exception $e) {
                        logger()->error("Failed to import media for article {$row->Id}: {$e->getMessage()}");
                    }
                }

                $this->progressBar->advance();
            });

        $this->finishProgressBar('Imported articles.');
    }
}
