<?php

declare(strict_types=1);

namespace App\Console\Commands\Import;

use App\Enums\User\Role;
use App\Models\Article;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Models\Party;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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
    protected $description = 'Import place ids from the old database.';
    private ?Collection $userIds = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->userIds = collect();
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

    private function importAuthors()
    {
        $query = $this->db
            ->table('authors');

        $this->createProgressBar(
            'Importing authors IDs...',
            $query->count()
        );

        $query->get()
            ->each(function (stdClass $row) {
                $user = User::factory()->create([
                    'name' => $row->Name,
                    'role' => Role::CONTRIBUTOR,
                ]);
                if(filled($row->Avatar))
                {
                    $user->addMediaFromUrl($row->Avatar)
                        ->toMediaCollection('avatar');
                }

                $this->userIds->put($row->Id, $user->id);
                $this->progressBar->advance();
            });

        $this->progressBar->finish('');

    }

    private function importArticles()
    {
        $query = $this->db
            ->table('articles')
            ->orderBy('articles.BallotId');

        $this->createProgressBar(
            'Importing articles IDs...',
            $query->count()
        );

        $electionsIds = Election::pluck('id', 'old_id');
        $media= $this->db->table('articlepictures')
            ->get()
            ->keyBy('ArticleId');

        $query->get()
            ->each(function (stdClass $row) use ($electionsIds, $media) {
                $article = Article::factory()->create([
                    'title' => $row->Title,
                    'author_id' => $this->userIds->get($row->AuthorId),
                    'election_id' => $electionsIds->get($row->BallotId),
                    'content' => $row->Body,
                    'embeds' => $row->Embed? [$row->Embed]: null,
                ]);


                if  (filled($pics=$media->get($row->Id)))
                {
                    try {
                        $article->addMedia(file_get_contents("$pics->Url"));
                    } catch (\Exception $e) {
                        $this->logError($pics->Url);
                        $this->logError($e->getMessage());
                    }
                }
                $this->progressBar->advance();
            });
    }
}
