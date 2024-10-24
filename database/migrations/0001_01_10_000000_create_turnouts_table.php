<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('turnouts', function (Blueprint $table) {
            $table->id();

            $table->boolean('has_issues')->default(false);

            $table->foreignIdFor(Election::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Country::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->smallInteger('county_id')
                ->unsigned()
                ->nullable();

            $table->foreign('county_id')
                ->references('id')
                ->on('counties');

            $table->mediumInteger('locality_id')
                ->unsigned()
                ->nullable();

            $table->foreign('locality_id')
                ->references('id')
                ->on('localities');

            $table->string('section');

            $table->mediumInteger('initial_permanent')->unsigned();
            $table->mediumInteger('initial_complement')->unsigned();
            $table->mediumInteger('permanent')->unsigned();
            $table->mediumInteger('complement')->unsigned();
            $table->mediumInteger('supplement')->unsigned();
            $table->mediumInteger('mobile')->unsigned();

            $table->integer('initial_total')
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    initial_permanent + initial_complement
                SQL);

            $table->integer('total')
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    permanent + complement + supplement + mobile
                SQL);

            $table->float('percent', 2)
                ->unsigned()
                ->virtualAs(<<<'SQL'
                    ROUND(total  / initial_total * 100, 2)
                SQL);

            // $table->unique(['election_id', 'locality_id']);
            $table->unique(['election_id', 'county_id', 'section']);
            // $table->unique(['election_id', 'country_id']);

            // $table->integer('eligible_voters')->unsigned();
            // $table->mediumInteger('total_votes')->unsigned();
            // $table->mediumInteger('null_votes')->unsigned();
            // $table->mediumInteger('votes_by_mail')->unsigned();
            // $table->mediumInteger('valid_votes')->unsigned();
            // $table->mediumInteger('total_seats')->unsigned(); // TODO: check if we need it. it's currently empty in the old database
            // $table->mediumInteger('coefficient')->unsigned();
            // $table->mediumInteger('threshold')->unsigned();
            // $table->mediumInteger('circumscription')->unsigned();
            // $table->mediumInteger('min_votes')->unsigned();

            // $table->mediumInteger('division')->unsigned();
            // $table->mediumInteger('mandates')->unsigned();
            // $table->mediumInteger('correspondence_votes')->unsigned();
            // $table->mediumInteger('permanent_lists_votes')->unsigned();
            // $table->mediumInteger('special_lists_votes')->unsigned();
            // $table->mediumInteger('supplementary_votes')->unsigned();
        });
    }
};
